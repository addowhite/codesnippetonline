<div class="snippet_container">
	<p class="snippet_title"><?=$title?></p><p class="snippet_language"><?=$language?></p>
	<div class="snippet_content">
		<div class="snippet_editor" id="editor_<?=$snippet_id?>"><?=$content?></div>
	</div>
	<p class="snippet_author">by <?=$author?></p>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
		var editor = ace.edit("editor_<?=$snippet_id?>");
	  editor.setTheme("ace/theme/monokai");
	  editor.getSession().setMode("ace/mode/<?=Infra::get_ace_language_name($language)?>");
		editor.setReadOnly(true);
		editor.renderer.setShowGutter(false);
	});
</script>
