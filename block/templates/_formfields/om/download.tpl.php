<div class="control-group radio-group">
  <label class="control-label" for="<%=field.key%>"><?= t('<%=blockhandle%>.label.<%=field.key%>')?></label>
  <div class="controls">
    <?php
    $<%='fileobject'+field.key%> = $data['<%=field.key%>'] > 0 ? File::getByID($data['<%=field.key%>']) : 0;
    echo $al->file('ccm-b-<%=field.type%>{{index}}', 'omcontents[{{index}}][<%=field.key%>]', t('choose.<%=field.type%>'), $<%='fileobject'+field.key%>);
    ?>
  </div>
</div>
