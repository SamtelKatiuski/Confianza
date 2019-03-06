<?php if(isset($this->_paginacion)): ?>

	<?php if($this->_paginacion['primero']):?>
		<a href="<?=$url . $this->_paginacion['primero']?>" class="pagination-options"><?= $this->_lang->firstPage ?></a>
	<?php else:?>
		Primero
	<?php endif;?>

&nbsp;

	<?php if($this->_paginacion['anterior']):?>
		<a href="<?=$url . $this->_paginacion['anterior']?>" class="pagination-options">< <?= $this->_lang->previousPage ?></a>
	<?php else:?>
		Anterior
	<?php endif;?>

&nbsp;	

	<?php for($i = 0 ; $i < count($this->_paginacion['rango']) ; $i++):?>
		<?php if($this->_paginacion['actual'] == $this->_paginacion['rango'][$i]):?>

			<span class="current"><?=$this->_paginacion['rango'][$i];?></span>&nbsp;

		<?php else:?>

			<a href="<?=$url . $this->_paginacion['rango'][$i]?>"><?=$this->_paginacion['rango'][$i];?></a>
			&nbsp;
		<?php endif;?>
	<?php endfor;?>

&nbsp;

	<?php if($this->_paginacion['siguiente']):?>
		<a href="<?=$url . $this->_paginacion['siguiente']?>" class="pagination-options"><?= $this->_lang->nextPage ?> ></a>
	<?php else:?>
		Siguiente
	<?php endif;?>

&nbsp;

	<?php if($this->_paginacion['ultimo']):?>
		<a href="<?=$url . $this->_paginacion['ultimo']?>" class="pagination-options"><?= $this->_lang->lastPage ?></a>
	<?php else:?>
		Ultimo
	<?php endif;?>



<?php endif;?>