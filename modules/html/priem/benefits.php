<?php
$companys = httpGetContent('Catalog_ПриемныеКампании');
$benefits = httpGetContent('Catalog_Льготы');

?>
<form action="/site/priem/benefits.pdf" method="get">
	<p>Приёмная компания:</p>
	<select name="company">
		<?php foreach ($companys as $company): ?>
			<option value="<?= $company['Ref_Key'] ?>"><?= $company['Description'] ?></option>
		<?php endforeach ?>
	</select>

	<p>Льгота:</p>
	<select name="benefit">
		<?php foreach ($benefits as $benefit): ?>
			<option value="<?= $benefit['Ref_Key'] ?>"><?= $benefit['Description'] ?></option>
		<?php endforeach ?>
	</select>

	<br />
	<br />
	<input type="submit" value="Создать">
</form>
