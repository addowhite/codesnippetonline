<div class="card snippet-container">
	<table class="snippet-title-table">
		<tr>
			<td><p class="title snippet-title"><?=$title?></p></td>
			<td><p class="snippet-language"><?=$language?></p></td>
		</tr>
	</table>
	<div class="snippet-content">
		<div class="snippet-editor" id="editor_<?=$snippet_id?>"><?=$content?></div>
	</div>
	<p class="snippet-author">by <?=$author?></p>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
		var editor = ace.edit("editor_<?=$snippet_id?>");
	  editor.setTheme("ace/theme/monokai");
	  editor.getSession().setMode("ace/mode/<?=Infra::get_ace_language_name($language)?>");
		editor.setReadOnly(true);
		editor.renderer.setShowGutter(false);
		editor.setShowPrintMargin(false);
		editor.setOption("maxLines", 20);
	});
</script>
