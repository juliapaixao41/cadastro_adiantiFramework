<?php
    class Pedido extends TRecord
    {
        const TABLENAME  = 'pedido';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'max';

        private $cliente;
        private $produto;
        

        public function __construct($id = null, $callObjectLoad = true)
        {

            parent::__construct($id, $callObjectLoad);

            parent::addAttribute('id_cliente');
            parent::addAttribute('id_produto');
            parent::addAttribute('qtd');
            parent::addAttribute('data_pedido');
            parent::addAttribute('valor');
            parent::addAttribute('obs');
        }

        public function get_cliente()
        {
            if (empty($this->cliente))
            {
                $this->cliente = new Cliente($this->cliente_id);
            }
            return $this->cliente;
        }

        public function get_produto()
        {
            if (empty($this->produto))
            {
                $this->produto = new Produto($this->produto_id);
            }
            return $this->produto;
        }


        
    }
