<?php
    class Venda extends TRecord
    {
        const TABLENAME  = 'venda';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'max';

        private $cliente;
        private $produto;

        public function __construct($id = null, $callObjectLoad = true)
        {

            parent::__construct($id, $callObjectLoad);

            parent::addAttribute('dt_venda');
            parent::addAttribute('valor');
            parent::addAttribute('qtd');
            parent::addAttribute('obs');
            parent::addAttribute('produto_id');
            parent::addAttribute('cliente_id');
        }

        

    }



