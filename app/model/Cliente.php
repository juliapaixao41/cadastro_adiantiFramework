<?php
    class Cliente extends TRecord
    {
        const TABLENAME  = 'cliente';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'max';

        public function __construct($id = null, $callObjectLoad = true)
        {

            parent::__construct($id, $callObjectLoad);

            parent::addAttribute('nome');
            parent::addAttribute('email');
            parent::addAttribute('tel');
            parent::addAttribute('dt_nasc');
            parent::addAttribute('endereco');
        }

    }
