<div class="control-group radio-group">
  <textarea class="advancedEditor ccm-advanced-editor ccm-advanced-editor{{index}}" name="omcontents[{{index}}][<%=field.key%>]" style="width: 580px; height: 250px">
    <?php $tiny<%=field.key%> = new <%=blockhandle%>Tiny($data['<%=field.key%>']); ?>
    <?php echo htmlspecialchars($tiny<%=field.key%>->getContentEditMode())?>
  </textarea>
</div>
