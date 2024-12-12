<?php

class FormularioPedido extends TPage
{

   
    private $form;
    protected $product_list;
    protected $detail_row;
    protected $dt_venda;

    public function __construct($param)
    {
        parent::__construct($param);


        $this->form = new BootstrapFormBuilder('form_SaleMultivalue');
        $this->form->setFormTitle('Pedidos');


        //Cliente
        $id            = new TEntry('id');
        $cliente_id    = new TDBSeekButton('cliente_id', 'cadastro', $this->form->getName(), 'cliente', 'nome', 'cliente_id', 'cliente_nome');
        $cliente_nome   = new TEntry('cliente_nome');
        $data_pedido   = new TDate ('data_pedido');
        $obs           = new TEntry ('obs');

        //Produto
        $produto_id    = new TEntry('produto_id');
        $nome_produto  = new TEntry('nome_produto');
        $preco_venda   = new TEntry('preco_venda');
        $qtd           = new  TEntry('qtd');
        $preco_total   = new  TEntry('preco_total');
        
        $id->setEditable(FALSE);
        $preco_venda->setEditable(FALSE);
        $id->setSize(60);
        $obs->setSize('100%',50);
        $cliente_id->setSize(50);
        $cliente_nome->setEditable(false);
        $cliente_nome->setSize('calc(100% - 200px)');
        $obs->setSize('100%',50);

        $cliente_id->addValidation('cliente', new TRequiredValidator);

        $data_pedido->setMask('dd/mm/yyyy');
        $preco_venda->setNumericMask(2, ',', '.', true);

        //Cliente

        $this->form->addFields( [new TLabel('Id')],            [$id]);
        $this->form->addFields( [new TLabel('Cliente')],       [$cliente_id, $cliente_nome]);
        $this->form->addFields( [new TLabel('Data Pedido')],   [$data_pedido]);
        $this->form->addFields( [new TLabel('Observação')],    [$obs]);


        //Produtos 

        $produto_id = new TDBUniqueSearch('produto_id[]', 'cadastro', 'produto', 'id', 'nome');
        $produto_id->setMinLength(1);
        $produto_id->setSize('100%');
        $produto_id->setMask('{nome} ({id})');
        $produto_id->setChangeAction(new TAction(array($this, 'onChangeProduct')));

        $preco_venda = new TEntry('preco_venda[]');
        $preco_venda->setNumericMask(2,',','.', true);
        $preco_venda->setSize('100%');
        $preco_venda->style = 'text-align: right';
    
        
        $qtd = new TEntry('qtd[]');
        $qtd->setSize('100%');
        $qtd->setExitAction(new TAction(array($this, 'onUpdateTotal')));
        $qtd->style = 'text-align: right';

        $this->form->addField($produto_id);
        $this->form->addField($preco_venda);
        $this->form->addField($qtd);
        //$this->form->addField($preco_total);

        $this->produto_lista = new TFieldList;
        $this->produto_lista->addField('<b>Produto</b>', $produto_id, ['width' => '40%']);
        $this->produto_lista->addField('<b>Preço</b>', $preco_venda, ['width' => '20%']);
        $this->produto_lista->addField('<b>Qtd.</b>', $qtd, ['width' => '20%']);
        //$this->produto_lista->addField( '<b>Total</b>',   $preco_total,  ['width' => '20%', 'sum' => true]);
        $this->produto_lista-> width = '100%';
        $this->produto_lista->enableSorting();

       
        $this->form->addFields( [new TFormSeparator('Produto') ] );
        $this->form->addFields( [$this->produto_lista] );

        $this->form->addAction( _t('Save'),  new TAction( [$this, 'onSave'] ),  'fa:save green' );
        $this->form->addAction( _t('Clear'), new TAction( [$this, 'onClear'] ), 'fa:eraser red' );
       

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }

    
     function onEdit($param)
        {
            try
            {
                TTransaction::open('cadastro');
            
                if (isset($param['key']))
                {
                    $key = $param['key'];
                
                    $venda = new Venda($key);
                    $this->form->setData($produto);
                
                    $venda_item = VendaItem::where('venda_id', '=', $venda->id)->load();
                
                    $this->produto_lista->addHeader();
                    if ($venda_itens)
                    {
                        foreach($venda_itens  as $item )
                        {
                            $item->preco_venda  = $item->venda_item;
                            $item->qtd = $item->qtd;
                            $item->preco_total  = $item->venda_item * $item->qtd;
                            $this->produto_lista->addDetail($item);
                        }
                        $this->produto_lista->addCloneAction();
                    }
                    else
                    {
                        $this->onClear($param);
                    }
                
                    TTransaction::close(); 
                }
            }
            catch (Exception $e) 
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }


       
        }

        public static function onChangeProduct($param)
        {

            $input_id = $param['_field_id'];
            $produto_id = $param['_field_value'];
            $input_pieces = explode('_', $input_id);
            $unique_id = end($input_pieces);
        
            if ($produto_id)
            {
                $response = new stdClass;
            
                try
                {
                    TTransaction::open('cadastro');
                    $produtos = Produto::find($produto_id);
                    $response->{'preco_venda'.$unique_id} = number_format($produto->preco_venda,2,',', '.');
                    $response->{'qtd'.$unique_id} = '1,00';
                    $response->{'preco_total'.$unique_id} = number_format($produto->venda_item,2,',', '.');
                
                    TForm::sendData('form_SaleMultiValue', $response);
                    TTransaction::close();
                }
                catch (Exception $e)
                {
                    TTransaction::rollback();
                }
            }
        }

        public static function onUpdateTotal($param)
        {
        
            $input_id = $param['_field_id'];
            $produto_id = $param['_field_value'];
            $input_pieces = explode('_', $input_id);
            $unique_id = end($input_pieces);
            parse_str($param['_field_data'], $field_data);
            $row = $field_data['row'];
        
            $venda_item = (double) str_replace(['.', ','], ['', '.'], $param['venda_item'][$row]);
            $qtd         = (double) str_replace(['.', ','], ['', '.'], $param['qtd'][$row]);
        
            $obj = new StdClass;
            $obj->{'product_total_'.$unique_id} = number_format( ($preco_venda * $qtd), 2, ',', '.');
            TForm::sendData('form_SaleMultiValue', $obj);
        }

    public function onClear($param)
    {
        $this->produto_lista->addHeader();
        $this->produto_lista->addDetail( new stdClass );
        $this->produto_lista->addCloneAction();
    }
    

    public static function onSave($param)
    {
        try
        {
            
            TTransaction::open('cadastro');
            
            $id = (int) $param['id'];
            $produto = new Produto($id);
            $produto->date = $param['date'];
            $produto->cliente_id = $param['cliente_id'];
            $produto->obs = $param['obs'];
            $total = 0;
            $produto->store();
            
            $produto_itens = ProdutoItem::where('produto_id', '=', $produto->id)->delete();
            
            if( !empty($param['produto_id']) AND is_array($param['produto_id']) )
            {
                foreach( $param['produto_id'] as $row => $produto_id)
                {
                    if ($produto_id)
                    {
                        $item = new ProdutoItem;
                        $item->produto_id  = $produto_id;
                        $item->venda_item  = (float) str_replace(['.',','], ['','.'], $param['preco_venda'][$row]);
                        $item->qtd      = (float) str_replace(['.',','], ['','.'], $param['qtd'][$row]);
                        $item->discount    = 0;
                        $item->total       = $item->venda_item * $item->qtd;
                        
                        $total += $item->total;
                        $item->produto_id = $produto->id;
                        $item->store();
                    }
                }
            }
            
            $produto->total = $total;
            $produto->store(); 
            
            $data = new stdClass;
            $data->id = $sale->id;
            TForm::sendData('form_SaleMultiValue', $data);
            TTransaction::close(); 
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

}

?>