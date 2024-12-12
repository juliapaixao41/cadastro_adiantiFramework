<?php
    class Produto extends TRecord
    {
        const TABLENAME  = 'produto';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'max';

        public function __construct($id = null, $callObjectLoad = true)
        {

            parent::__construct($id, $callObjectLoad);

            parent::addAttribute('nome');
            parent::addAttribute('qtd');
            parent::addAttribute('preco_compra');
            parent::addAttribute('preco_venda');
            parent::addAttribute('descricao');
            parent::addAttribute('dt_cadastro');
        }

    }
