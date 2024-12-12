<?php

class FormularioCadastro extends TPage
{
    private $form;
    private $datagrid;
    private $loaded;

    public function __construct(){

        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Cadastro de Cliente');

        $this->datagrid = new BootstrapDatagridWrapper (new TDataGrid);
        $this->datagrid->width = '100%';


        //$id       = new TEntry('id');
        $nome     = new TEntry('nome');
        $email    = new TEntry('email');
        $dt_nasc  = new TDate ('dt_nasc');
        $tel      = new TEntry('tel');
        $endereco = new TEntry('endereco');

        $dt_nasc->setMask('dd/mm/yyyy');
        $tel->setMask('(99) 99999-9999');

    
        //$this->form->addFields( [new TLabel('Id')],            [$id]);
        $this->form->addFields( [new TLabel('Nome')],          [$nome]);
        $this->form->addFields( [new TLabel('E-mail')],        [$email]);
        $this->form->addFields( [new TLabel('Dt Nascimento')], [$dt_nasc], [new TLabel('Telefone')], [$tel]);
        $this->form->addFields( [new TLabel('Endereço')],      [$endereco]);

        $this->form->addAction( 'Cadastrar', new TAction( [$this, 'onSend']), 'fa:save green');


        parent::add( $this->form );


        //Adição do DataGrid

        $col_id       = new TDataGridColumn('id',       'Cód', null           );
        $col_nome     = new TDataGridColumn('nome',     'Nome', null          );
        $col_tel      = new TDataGridColumn('tel',      'Telefone', null      );
        $col_dtNasc   = new TDataGridColumn('dt_nasc',  'Dt Nascimento', null );
        $col_email    = new TDataGridColumn('email',    'E-mail', null        );
        $col_endereco = new TDataGridColumn('endereco', 'Endereço', null      );



        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_tel);
        $this->datagrid->addColumn($col_dtNasc);
        $this->datagrid->addColumn($col_email);
        $this->datagrid->addColumn($col_endereco);

       


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

           $data = $this->form->getData();

           $cliente = new Cliente;
           $cliente->fromArray( (array) $data);
           $cliente->store();

           new TMessage('info', 'Registro salvo com sucesso!');

           TApplication::loadPage('FormularioCadastro');

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

            $repository = new TRepository('Cliente');

            $limit = 10;

            $criteria = new TCriteria;
            $criteria->setProperty('limit', $limit);
            $criteria->setProperties($param);

            $clientes = $repository->load($criteria);

            $this->datagrid->clear();

            if($clientes)
            {
                foreach ($clientes as $cliente)
                {
                    $this->datagrid->addItem($cliente);
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