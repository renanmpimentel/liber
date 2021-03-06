<?php

class ProdutosController extends AppController {
	var $name = 'Produtos';
	var $components = array('RequestHandler');
	var $helpers = array('Ajax', 'Javascript');

	/**
	 * Obtem dados necessarios ao decorrer deste controller.
	 * Os dados sao setados em variaveis a serem utilizadas nas views 
	 */
	function _obter_opcoes() {
		$this->loadModel('ProdutoCategoria');
		$this->ProdutoCategoria->recursive = -1;
		$consulta1 = $this->ProdutoCategoria->find('list',array('fields'=>array('ProdutoCategoria.id','ProdutoCategoria.nome')));
		$this->set('opcoes_categoria_produto',array_merge(array(0=>''),$consulta1));
	}
	
	function index() {
		if ( $this->RequestHandler->isAjax() ) {
			$this->layout = 'default_ajax';
		}
		$this->paginate = array (
			'limit' => 10,
			'order' => array (
				'Produto.id' => 'desc'
			),
		    'contain' => array()
		);
		$dados = $this->paginate('Produto');
		$this->set('consulta',$dados);
	}
	
	function cadastrar() {
		if ( $this->RequestHandler->isAjax() ) {
			$this->layout = 'default_ajax';
		}
		$this->_obter_opcoes();
		if (! empty($this->request->data)) {
			if ($this->request->data['Produto']['categoria_produto_id'] == 0) $this->request->data['Produto']['categoria_produto_id'] = null;
			if ($this->Produto->save($this->request->data)) {
				$this->Session->setFlash('Produto cadastrado com sucesso.','flash_sucesso');
				$this->redirect($this->referer(array('action' => 'index')));
			}
			else {
				$this->Session->setFlash('Erro ao cadastrar o produto.','flash_erro');
			}
		}
	}
	
	function editar($id=NULL) {
		if ( $this->RequestHandler->isAjax() ) {
			$this->layout = 'default_ajax';
		}
		$this->_obter_opcoes();
		if (empty ($this->request->data)) {
			$this->Produto->recursive = -1;
			$this->Produto->id = $id;
			$this->request->data = $this->Produto->read();
			if ( ! $this->request->data) {
				$this->Session->setFlash("Produto $id não encontrado.",'flash_erro');
				$this->redirect(array('action'=>'index'));
			}
		}
		else {
			$this->request->data['Produto']['id'] = $id;
			if ($this->request->data['Produto']['categoria_produto_id'] == 0) $this->request->data['Produto']['categoria_produto_id'] = null;
			if ($this->Produto->save($this->request->data)) {
				$this->Session->setFlash("Produto $id atualizado com sucesso.",'flash_sucesso');
				$this->redirect(array('action'=>'index'));
			}
			else {
				$this->Session->setFlash('Erro ao atualizar o produto.','flash_erro');
			}
		}
	}
	
	function excluir($id=NULL) {
		if ( $this->RequestHandler->isAjax() ) {
			$this->layout = 'default_ajax';
		}
		if (! empty($id)) {
			if ($this->Produto->delete($id)) $this->Session->setFlash("Produto $id excluído com sucesso.",'flash_sucesso');
			else $this->Session->setFlash("Produto $id não pode ser excluído.",'flash_erro');
			$this->redirect(array('action'=>'index'));
		}
		else {
			$this->Session->setFlash('Produto não informado.','flash_erro');
		}
	}

	function pesquisar() {
		if ( $this->RequestHandler->isAjax() ) {
			$this->layout = 'default_ajax';
		}
		$this->_obter_opcoes();
		if (! empty($this->request->data)) {
			//usuario enviou os dados da pesquisa
			$url = array('action'=>'pesquisar');
			$params = array_merge($url,$this->request->data['Produto']);
			$this->redirect($params);
		}
		
		if (! empty($this->request->params['named'])) {
			//a instrucao acima redirecionou para cá
			$dados = $this->request->params['named'];
			$condicoes=array();
			if (! empty($dados['nome'])) $condicoes[] = array('Produto.nome LIKE'=>'%'.$dados['nome'].'%');
			if (! empty($dados['categoria_produto_id'])) $condicoes[] = array('Produto.categoria_produto_id'=>$dados['categoria_produto_id']);
			if (! empty($dados['tipo_produto'])) $condicoes[] = array('Produto.tipo_produto'=>$dados['tipo_produto']);
			if (! empty($dados['codigo_ean'])) $condicoes[] = array('Produto.codigo_ean'=>$dados['codigo_ean']);
			if (! empty($dados['codigo_dun'])) $condicoes[] = array('Produto.codigo_dun'=>$dados['codigo_dun']);
			if (! empty($dados['unidade'])) $condicoes[] = array('Produto.unidade'=>$dados['unidade']);
			if (! empty($dados['quantidade_estoque_fiscal'])) $condicoes[] = array('Produto.quantidade_estoque_fiscal'=>$dados['quantidade_estoque_fiscal']);
			if (! empty($dados['situacao'])) $condicoes[] = array('Produto.situacao'=>$dados['situacao']);
			if (! empty ($condicoes)) {
				$this->paginate = array (
					'limit' => 10,
					'order' => array (
						'Produto.id' => 'desc'
					),
				'contain' => array()
				);
				$resultados = $this->paginate('Produto',$condicoes);
				if (! empty($resultados)) {
					$num_encontrados = count($resultados);
					$this->set('resultados',$resultados);
					$this->set('num_resultados',$num_encontrados);
					$this->Session->setFlash("$num_encontrados produto(s) encontrado(s)",'flash_sucesso');
				}
				else $this->Session->setFlash("Nenhum produto encontrado",'flash_erro');
			}
			else {
				$this->set('num_resultados','0');
				$this->Session->setFlash("Nenhum resultado encontrado",'flash_erro');
			}
		}
	}

	function pesquisaAjaxProduto($campo_a_pesquisar,$termo = null) {
		if (strtoupper($campo_a_pesquisar) == "NOME") $campo = 'nome';
		else if (strtoupper($campo_a_pesquisar) == "CODIGO") $campo = 'id';
		else return null;
		if (! isset($termo)) $termo = $this->request['url']['term'];
		if ( $this->RequestHandler->isAjax() ) {
			$i=0;
			$resultados=array();
			$retorno=array();
			$r = array();
   			Configure::write ('debug',0);
   			$this->autoRender=false;
			if ($campo == 'id') {
				$condicoes = array('Produto.id'=>$termo);
			}
			else {
				$condicoes = array("Produto.$campo LIKE" => '%'.$termo.'%');
			}
			$this->Produto->recursive = -1;
			$resultados = $this->Produto->find('all',array('conditions'=>$condicoes));
			if (!empty($resultados)) {
				foreach ($resultados as $r) {
					$retorno[$i]['label'] = $r['Produto']['nome'];
					$retorno[$i]['value'] = $r['Produto'][$campo];
					$retorno[$i]['id'] = $r['Produto']['id'];
					$retorno[$i]['nome'] = $r['Produto']['nome'];
					$retorno[$i]['eh_vendido'] = ($r['Produto']['tipo_produto'] != 'M') ? 1 : 0;
					$retorno[$i]['tipo_produto'] = $r['Produto']['tipo_produto'];
					$retorno[$i]['fora_de_linha'] = ($r['Produto']['situacao'] == 'F') ? 1 : 0;
					$retorno[$i]['situacao'] = $r['Produto']['situacao'];
					$retorno[$i]['preco_custo'] = $r['Produto']['preco_custo'];
					$retorno[$i]['preco_venda'] = $r['Produto']['preco_venda'];
					$retorno[$i]['estoque_disponivel'] = $r['Produto']['quantidade_estoque_fiscal'] - $r['Produto']['quantidade_reservada'];
					$retorno[$i]['tem_estoque_ilimitado'] = $r['Produto']['tem_estoque_ilimitado'];
					$i++; 
				}
				print json_encode($retorno);
			}
		}
	}
	
}

?>