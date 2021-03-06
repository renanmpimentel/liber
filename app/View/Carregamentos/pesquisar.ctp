<script type="text/javascript">
		$(function(){
			$(".datepicker").datepicker({
				showOn: "button",
				buttonImage: "<?php print $this->Html->url('/',true); ?>/img/calendario_icone.gif",
				buttonImageOnly: true
			});
	
		});
</script>
<h2 class="descricao_cabecalho">Pesquisar carregamento</h2>

<?php
array_unshift($opcoes_situacoes,array(''=>''));
array_unshift($opcoes_motoristas,array(''=>''));
array_unshift($opcoes_veiculos,array(''=>''));
/**
 * Elimino as divs dos campos input para que nao apareça quais campos
 * sao marcados como obrigatorios no BD, pois aqui isso non ecxiste
 */
print $this->Form->create(null,array('controller'=>'carregamentos','action'=>'pesquisar','autocomplete'=>'off'));
?>
<div class="divs_grupo_2">
	
	<div class="div1_2">
		<?php
		print '<div>'.$this->Form->input('data_inicial', array('label'=>'Data Inicial','div'=>false,'class'=>'datepicker mascara_data')).'</div>';
		print '<div>'.$this->Form->input('data_final',array('div'=>false,'class'=>'datepicker mascara_data')).'</div>';
		print '<div>'.$this->Form->input('situacao',array('div'=>false,'label'=>'Situação','options'=>$opcoes_situacoes)).'</div>';
		?>
	</div>
	
	<div class="div2_2">
		<?php
		print '<div>'.$this->Form->input('descricao',array('label'=>'Descrição','div'=>false)).'</div>';
		print '<div>'.$this->Form->input('motorista',array('label'=>'Motorista','div'=>false,'options'=>$opcoes_motoristas)).'</div>';
		print '<div>'.$this->Form->input('veiculo',array('label'=>'Veículo','div'=>false,'options'=>$opcoes_veiculos)).'</div>';
		?>
	</div>

<div class="limpar">&nbsp;</div>

	<?php
	print $this->Form->end('Pesquisar');	
	?>
	
</div>

<?php if (isset($num_resultados) && $num_resultados > 0) : ?>
	<table class="padrao">
		<thead>
			<tr>
				<th><?php print $this->Paginator->sort('id','Cód'); ?></th>
				<th><?php print $this->Paginator->sort('data_hora_criado','Criado em'); ?></th>
				<th><?php print $this->Paginator->sort('situacao','Situação'); ?></th>
				<th><?php print $this->Paginator->sort('descricao','Descrição'); ?></th>
				<th><?php print $this->Paginator->sort('motorista','Motorista'); ?></th>
				<th><?php print $this->Paginator->sort('veiculo','Veículo'); ?></th>
				<th colspan="2">Ações</th>
			</tr>
		</thead>
		
		<tbody>
			<?php foreach ($resultados as $r) : ?>
				<tr>
					<td><?php print $r['Carregamento']['id']; ?></td>
					<td><?php print $this->Html->link($r['Carregamento']['data_hora_criado'],'detalhar/' . $r['Carregamento']['id']) ;?></td>
					<td><?php print $opcoes_situacoes[$r['Carregamento']['situacao']]; ?></td>
					<td><?php print $r['Carregamento']['descricao']; ?></td>
					<td><?php print $r['Motorista']['nome']; ?></td>
					<td><?php print $r['Veiculo']['placa']; ?></td>
					<td>
					<?php print '<a title="Excluir" onclick="javascript: return confirm(\'Deseja realmente excluir este registro?\')"
					href="'.$this->Html->url(array('action'=>'excluir')).'/'.$r['Carregamento']['id'].'">'.
					$this->Html->image('del24x24.png', array('alt'=>'Excluir'))
					.'</a>';?>
					</td>
					<td><?php print $this->Html->image('detalhar24x24.png',array('title'=>'Detalhar',
					'alt'=>'Detalhar','url'=>array('action'=>'detalhar',$r['Carregamento']['id']))) ?></td>
				</tr>
			<?php endforeach; ?>
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
	
<?php endif; ?>
