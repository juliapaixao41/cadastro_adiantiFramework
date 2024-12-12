<?php

class FormularioPedido extends TPage
{

   
    private $form;
    protected $product_list;
    protected $dt_venda;


    public function __construct()
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder('form_SaleMultivalue');
        $this->form->setFormTitle('Pedidos');


        //Cliente
        $id            = new TEntry('id');
        $cliente_id    = new TDBSeekButton('cliente_id', 'cadastro', $this->form->getName(), 'cliente', 'nome', 'cliente_id', 'cliente_nome');
        $cliente_nome  = new TEntry('cliente_nome');
        $dt_venda      = new TDate ('dt_venda');
        $obs           = new TEntry ('obs');

        //Produto
        $id_produto    = new TEntry('id_produto');
        $nome_produto  = new TEntry('nome_produto');
        $preco_venda   = new TEntry('preco_venda');
        $qtd           = new TEntry('qtd');
        
        
        $id->setEditable(FALSE);
        $cliente_nome->setEditable(FALSE);
        $id->setSize(60);
        $obs->setSize('100%',50);
        $cliente_id->setSize(50);
        $cliente_nome->setSize('calc(100% - 200px)');
        $obs->setSize('100%',50);

        $cliente_id->addValidation('cliente', new TRequiredValidator);
        $cliente_nome->addValidation('cliente', new TRequiredValidator);

        $nome_produto->addValidation('cliente', new TRequiredValidator);
        $preco_venda->addValidation('cliente', new TRequiredValidator);

        $dt_venda->setMask('dd/mm/yyyy');
        $preco_venda->setNumericMask(2, ',', '.', true);

        //Cliente

        $this->form->addFields( [new TLabel('Id')],            [$id]);
        $this->form->addFields( [new TLabel('Cliente')],       [$cliente_id, $cliente_nome]);
        $this->form->addFields( [new TLabel('Data Venda')],    [$dt_venda]);
        $this->form->addFields( [new TLabel('Observação')],    [$obs]);


        //Produtos 

        $produto_id = new TDBUniqueSearch('produto_id[]', 'cadastro', 'produto', 'id', 'nome');
        $produto_id->setMinLength(1);
        $produto_id->setSize('100%');
        $produto_id->setMask('{nome}');
        $produto_id->setChangeAction(new TAction(array($this, 'onChangeProduct')));

        $produto_valor = new TEntry('preco_venda[]');
        $produto_valor->setNumericMask(2,',','.', true);
        $produto_valor->setSize('100%');
        $produto_valor->style = 'text-align: right';
        $produto_valor->setEditable(false);
        
       
    
        $quantidade = new TEntry('qtd[]');
        $quantidade->setNumericMask(2,',','.', true);
        $quantidade->setSize('100%');
        $quantidade->style = 'text-align: right';
        

        $this->form->addField($produto_id);
        $this->form->addField($produto_valor);
        $this->form->addField($quantidade);

        $this->produto_lista = new TFieldList;
        $this->produto_lista->addField('<b>Produto</b>', $produto_id, ['width' => '40%']);
        $this->produto_lista->addField('<b>Preço</b>', $preco_venda, ['width' => '20%']);
        $this->produto_lista->addField('<b>Qtd.</b>', $qtd, ['width' => '20%']);
        
        
        $this->produto_lista-> width = '100%';
        $this->produto_lista->enableSorting();

       
        $this->form->addFields( [new TFormSeparator('Produto') ] );
        $this->form->addFields( [$this->produto_lista] );

        $this->produto_lista->addHeader();
        $this->produto_lista->addDetail( new stdClass );
        $this->produto_lista->addCloneAction();

        $this->form->addAction( _t('Save'),  new TAction( [$this, 'onSave'] ),  'fa:save green' );
       

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }

    

        public static function onChangeProduct($param)
        {
                
        }

    

    public function onSave($param)
    {

        try
        {
            TTransaction::open('cadastro');

            $this->form->validate();

           
            $id = (int) $param['id'];
            $venda = new Venda($id);
            $venda->dt_venda = $param['dt_venda'];
            $venda->cliente_id = $param['cliente_id'];
            $venda->obs = $param['obs'];
            $venda->store();

            $this->form->setData($venda);

          

           new TMessage('info', 'Registro salvo com sucesso!');
            

            TApplication::loadPage('FormularioPedido');
            TTransaction::close(); 


        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
       

    }

    public static function onUpdateTotal($param)
    {

    }
}

?>