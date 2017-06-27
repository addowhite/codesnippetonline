<form id="edit_post_form" action="/processes/edit_snippet.php" method="post">
  <input class="post_title top" type="text" name="title" placeholder="Title" value="<?=$post_title?>"><!--
--><select class="privacy-dropdown" name="privacy">
    <option value="public">Public</option>
    <option value="private">Private</option>
  </select><!--
--><select id="language_dropdown" class="language-dropdown" name="language">
    <option value="c++">C++</option>
    <option value="c#">C#</option>
    <option value="c">C</option>
    <option value="java">Java</option>
    <option value="javascript">Javascript</option>
    <option value="php">PHP</option>
    <option value="lua">LUA</option>
  </select><!--
--><textarea id="hidden_textarea" name="content"></textarea><div id="editor">// Code here...</div>
</form>
<div class="button-container">
  <input form="edit_post_form" class="button right" type="submit" name="submit" value="<?=$save_button_text?>">
</div>
<script src="../../ace/ace.js" type="text/javascript" charset="utf-8"></script>
<script>

var editor;

var languages = {
  "c++"        : "c_cpp",
  "c#"         : "csharp",
  "c"          : "c_cpp",
  "java"       : "java",
  "javascript" : "javascript",
  "php"        : "php",
  "lua"        : "lua"
};

function onPageLoad() {
  editor = ace.edit("editor");
  editor.setTheme("ace/theme/monokai");
  editor.getSession().setMode("ace/mode/javascript");
  editor.setShowPrintMargin(false);

  document.getElementById("language_dropdown").addEventListener("change", updateEditorLanguage);
  document.getElementById("edit_post_form").addEventListener("submit", updateHiddenTextareaValue);

  updateEditorLanguage();
  updateHiddenTextareaValue();
}

function updateEditorLanguage() {
  editor.getSession().setMode("ace/mode/" + languages[document.getElementById("language_dropdown").value]);
}

function updateHiddenTextareaValue() {
  document.getElementById("hidden_textarea").value = editor.getValue();
}

document.addEventListener("DOMContentLoaded", onPageLoad);
</script>
