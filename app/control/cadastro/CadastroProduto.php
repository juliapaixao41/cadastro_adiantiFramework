<?php

class CadastroProduto extends TPage
{
    private $form;
    private $datagrid;
    private $loaded;
    

    public function __construct(){

        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Cadastro de Produto');

        $this->datagrid = new BootstrapDatagridWrapper (new TDataGrid);
        $this->datagrid->width = '100%';


    
        $id            = new TEntry('id');
        $nome          = new TEntry('nome');
        $qtd           = new TEntry('qtd');
        $preco_compra  = new TEntry ('preco_compra');
        $preco_venda   = new TEntry('preco_venda');
        $dt_cadastro   = new TDateTime('dt_cadastro');
        $descricao     = new TEntry('descricao');
        $id->setEditable(FALSE);

        $dt_cadastro->setMask('dd/mm/yyyy hh:ii');
        $preco_compra->setNumericMask(2, ',', '.', true);
        $preco_venda->setNumericMask(2, ',', '.', true);

        $dt_cadastro->setSize('100%');

        $dt_cadastro->setValue( date('d-m-Y H:i') );
       

    
        $this->form->addFields( [new TLabel('id')],            [$id]);
        $this->form->addFields( [new TLabel('Nome')],          [$nome]);
        $this->form->addFields( [new TLabel('Qtd.')],          [$qtd], [new TLabel('Dt. Cadastro')], [$dt_cadastro]);
        $this->form->addFields( [new TLabel('Preço Compra')],  [$preco_compra], [new TLabel('Preço Venda')], [$preco_venda]);
        $this->form->addFields( [new TLabel('Descrição')],     [$descricao]);

        $this->form->addAction( 'Cadastrar', new TAction( [$this, 'onSend']), 'fa:save green');


        parent::add( $this->form );


        //Adição do DataGrid
     
        $col_id           = new TDataGridColumn('id',            'Cód', null           );
        $col_nome         = new TDataGridColumn('nome',          'Nome', null          );
        $col_qtd          = new TDataGridColumn('qtd',           'QTD.', null          );
        $col_precocompra  = new TDataGridColumn('preco_compra',  'Preço Compra', null  );
        $col_precoVenda   = new TDataGridColumn('preco_venda',   'Preço Venda', null   );
        $col_dtCadastro   = new TDataGridColumn('dt_cadastro',   'Dt. Cadastro', null  );
        $col_descricao    = new TDataGridColumn('descricao',     'Descrição',    null  );



        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_qtd);
        $this->datagrid->addColumn($col_precocompra);
        $this->datagrid->addColumn($col_precoVenda);
        $this->datagrid->addColumn($col_dtCadastro);
        $this->datagrid->addColumn($col_descricao);


        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction ( new TAction([$this, 'onReload']));


        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        
        parent::add($panel);
    }

    public function onSend ($param)
    {

        try
        {
            TTransaction::open('cadastro');

            $this->form->validate();

           $data = $this->form->getData();

           $produto = new Produto;
           $produto->fromArray( (array) $data);
           $produto->store();

           $this->form->setData($produto);

           new TMessage('info', 'Registro salvo com sucesso!');

           TApplication::loadPage('CadastroProduto');

            TTransaction::close();

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

        
    }

    

    public function onReload($param)
    {

        try
        {
            TTransaction::open('cadastro');

            $repository = new TRepository('Produto');

            $limit = 10;

            $criteria = new TCriteria;
            $criteria->setProperty('limit', $limit);
            $criteria->setProperties($param);

            $produtos = $repository->load($criteria);

            $this->datagrid->clear();

            if($produtos)
            {
                foreach ($produtos as $produto)
                {
                    $this->datagrid->addItem($produto);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            $this->loaded = true;

            

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());

        }


    }

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('cadastro');

            if(isset($param['id']))
            {
                $key = $param['id'];
                $produto = new Produto($key);
                $this->form->setData($produto);

            }
            else 
            {
                $this->form->clear(true);
            }

            TTransaction::close();
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function OnClear($param)
    {
        $this->form->clear(true);

    }

    function show()
    {
        if (!$this->loaded)
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }


}
?>