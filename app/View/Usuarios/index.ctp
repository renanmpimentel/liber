
<h2 class="descricao_cabecalho">Exibindo todos os usuários cadastrados</h2>

<?php print $this->element('painel_index'); ?>

<table class="padrao">
	<thead>
		<tr>
			<th><?php print $this->Paginator->sort('id','Cód'); ?></th>
			<th><?php print $this->Paginator->sort('nome','Nome'); ?></th>
			<th><?php print $this->Paginator->sort('login','Login'); ?></th>
			<th><?php print $this->Paginator->sort('tipo','Tipo'); ?></th>
			<th><?php print $this->Paginator->sort('ativo','Ativo'); ?></th>
			<th><?php print $this->Paginator->sort('email','E-mail'); ?></th>
			<th><?php print $this->Paginator->sort('ultimo_login','Último login'); ?></th>
			<th><?php print $this->Paginator->sort('ultimo_logout','Último logout'); ?></th>
			<th colspan="2">Ações</th>
		</tr>
	</thead>
	
	<tbody>
		
<?php foreach ($consulta_usuario as $usuario): ?>
		
		<tr>
			<td><?php print $usuario['Usuario']['id'];?></td>
			<td><?php print $this->Html->link($usuario['Usuario']['nome'],'editar/' . $usuario['Usuario']['id']) ;?></td>
			<td><?php print $usuario['Usuario']['login']; ?></td>
			<td><?php print $usuario['Usuario']['tipo']; ?></td>
			<td>
				<?php
				if ($usuario['Usuario']['ativo'] == 1) print "Sim";
				else print "Não";
				?>
			</td>
			<td><?php print $usuario['Usuario']['email']; ?></td>
			<td><?php print $usuario['Usuario']['ultimo_login']; ?></td>
			<td><?php print $usuario['Usuario']['ultimo_logout']; ?></td>
			<td>
				<?php print $this->element('painel_editar',array('id'=>$usuario['Usuario']['id'])) ;?>
			</td>
			<td>
				<?php print $this->element('painel_excluir',array('id'=>$usuario['Usuario']['id'])) ;?>
			</td>
		</tr>

<?php endforeach ?>

	</tbody>
</table>

<?php
print $this->Paginator->counter(array(
	'format' => 'Exibindo %current% registros de um total de %count% registros. Página %page% de %pages%.'
)); 

print '<br/>';

print $this->Paginator->prev('« Anterior ', null, null, array('class' => 'disabled'));
print $this->Paginator->next(' Próximo »', null, null, array('class' => 'disabled'));

?>
