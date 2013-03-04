<?php if (count($searchErrors)): ?>
<div id="search_errors">
  <h2><?php echo __("Se han encontrado los siguientes errores")?></h2>
  <ul>
  <?php foreach(($searchErrors) as $error):?>
    <li><?php echo $error ?>
  <?php endforeach?> 
  </ul>
  <h2><?php echo __("Ayuda del buscador")?></h2>
  <p>
    <?php echo __("Las búsquedas pueden ser palabras o frases (conjunto de palabras entre comillas dobles). Además pueden emplearse wildcards como * que sustituirá varios caracteres o ? que sustituye sólo un caracter. Ejemplos de búsquedas son:")?>
    <br />
    <span class="search-sample"> <?php echo __('palabra1 palabra2 "una frase exacta"') ?> </span>
    <br />
    <?php echo __('Buscará palabra1 ó palabra2 ó "una frase exacta"')?>
  </p>
  <p>
    <?php echo __("Si se desea forzar la ocurrencia de una palabra se debe preceder con el símbolo +, por ejemplo:") ?>
    <br />
    <span class="search-sample"> <?php echo __('+palabra1 +palabra2') ?> </span>
    <br />
    <?php echo __('Buscará palabra1 y palabra2')?>
  </p>
  <p>
    <?php echo __("Si se desea forzar la NO ocurrencia de una palabra se debe preceder con el símbolo -, por ejemplo:") ?>
    <br />
    <span class="search-sample"> <?php echo __('+palabra1 -palabra2') ?> </span>
    <br />
    <?php echo __('Buscará palabra1 y NO palabra2')?>
  </p>
  <p>
    <?php echo __("Tenga en cuenta que no se admiten búsquedas que contengan los símbolos menor (<) ni (>), por ejemplo:") ?>
    <br />
    <span class="search-sample"> <?php echo htmlentities(__('pa<labra1 +palabra2 <li>')) ?> </span>
    <br />
    <?php echo __('Realizará la siguiente búsqueda:')?>
    <br />
    <span class="search-sample"> <?php echo __('pa labra1 +palabra2 li') ?> </span>
    <br />
  </p>
</div>
<?php endif?>
