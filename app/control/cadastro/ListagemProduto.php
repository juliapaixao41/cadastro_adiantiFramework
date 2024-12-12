<?php

class ListagemProduto extends TPage
{
    
    private $datagrid;
    private $loaded;
    private $form;


    public function __construct(){

        parent::__construct();
        
       $this->form = new BootstrapFormBuilder;
       $this->form->setFormTitle('Busca Produtos');

        $nome = new TEntry('nome');

        $this->form->addFields( [new TLabel('Nome')], [$nome] );

        $this->form->addAction('Buscar', new TAction([ $this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo Produto', new TAction(['CadastroProduto', 'onClear']), 'fa:plus-circle green');

        $this->datagrid = new BootstrapDatagridWrapper (new TDataGrid);
        $this->datagrid->width = '100%';

        //Adição do DataGrid
     
        $col_id           = new TDataGridColumn('id',            'Cód', null           );
        $col_nome         = new TDataGridColumn('nome',          'Nome', null          );
        $col_qtd          = new TDataGridColumn('qtd',           'QTD.', null          );
        $col_precocompra  = new TDataGridColumn('preco_compra',  'Preço Compra', null  );
        $col_precoVenda   = new TDataGridColumn('preco_venda',   'Preço Venda', null   );
        $col_dtCadastro   = new TDataGridColumn('dt_cadastro',   'Dt. Cadastro', null  );
        $col_descricao    = new TDataGridColumn('descricao',     'Descrição',    null  );


        //Adição do Colunas da DataGrid

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_qtd);
        $this->datagrid->addColumn($col_precocompra);
        $this->datagrid->addColumn($col_precoVenda);
        $this->datagrid->addColumn($col_dtCadastro);
        $this->datagrid->addColumn($col_descricao);

        $action1 = new TDataGridAction( ['CadastroProduto', 'onEdit'], ['key' => '{id}'] );
        $action2 = new TDataGridAction([$this, 'onDelete'] , ['key' => '{id}'] );

        $this->datagrid->addAction( $action1, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction( $action2, 'Excluir', 'fa:trash red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction ( new TAction([$this, 'onReload']));


       

        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        $vbox->add($panel);

        
        parent::add( $vbox );
    }


    public function onSend ($param)
    {

        try
        {
            TTransaction::open('cadastro');

           $data = $this->form->getData();

           $produto = new Produto;
           $produto->fromArray( (array) $data);
           $produto->store();

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

    public function onSearch($param)
    {
        $data = $this->form->getData();

        if(isset($data->nome))
        {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%");

            TSession::setValue('ListagemProduto_filter', $filter);
           


            $this->form->setData($data);
        }

        $this->onReload( [] );
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

            if (TSession::getValue('ListagemProduto_filter'))
            {
                $criteria->add(TSession::getvalue('ListagemProduto_filter'));
            }

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

    public function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        new TQuestion('Deseja excluir o produto?', $action);

    }

    

    public function Delete($param)
    {
        
        try
        {
            TTransaction::open('cadastro');

            $key = $param['key'];

            $produto = new Produto;
            $produto->delete($key);

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Produto excluido', $pos_action);

            TTransaction::close();

        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
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