<div class="card">
  <form id="edit_post_form" action="/processes/edit_snippet.php" method="post">
    <table>
      <tr>
        <td>
          <input class="post_title" type="text" name="title" placeholder="Title" value="<?=$snippet_title?>">
        </td>
        <td class="dropdown-container">
          <select id="privacy_dropdown" class="privacy-dropdown" name="privacy">
            <option value="public">Public</option>
            <option value="private">Private</option>
          </select>
        </td>
        <td class="dropdown-container">
          <select id="language_dropdown" class="language-dropdown" name="language">
            <option value="c++">C++</option>
            <option value="c#">C#</option>
            <option value="c">C</option>
            <option value="java">Java</option>
            <option value="javascript">Javascript</option>
            <option value="python">Python</option>
            <option value="lua">LUA</option>
            <option value="php">PHP</option>
            <option value="html">HTML</option>
          </select>
        </td>
      </tr>
    </table>
      
    <textarea id="hidden_textarea" name="content"></textarea>
    <div id="editor"><?=$snippet_content?></div>

  </form>
  
  <?php
    if ($allow_edit)
      echo "<div class=\"button-container\"><input form=\"edit_post_form\" class=\"button right\" type=\"submit\" name=\"submit\" value=\"$save_button_text\"></div>";
  ?>
  
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
  "python"     : "python",
  "lua"        : "lua",
  "php"        : "php",
  "html"       : "html"
};

function onPageLoad() {
  editor = ace.edit("editor");
  editor.setTheme("ace/theme/monokai");
  editor.getSession().setMode("ace/mode/javascript");
  editor.setShowPrintMargin(false);
  editor.on("input", updateEditorHeight);

  <?php
  if (!$allow_edit) {
    echo 'editor.setReadOnly(true);
          document.getElementById("privacy_dropdown").disabled = true;
          document.getElementById("language_dropdown").disabled = true;';
  }
  ?>

  // Set the value of the dropdown to the privacy status of the snippet
  document.getElementById("privacy_dropdown").value = "<?=$snippet_privacy_status?>" || "public";

  // Set the value of the dropdown to the programming language of the snippet
  document.getElementById("language_dropdown").value = "<?=$snippet_language?>" || "javascript";

  // Update the syntax highlighting when the user selects a language from the dropdown
  document.getElementById("language_dropdown").addEventListener("change", updateEditorLanguage);

  // Update the value of the hidden textarea when the user submits the form
  document.getElementById("edit_post_form").addEventListener("submit", updateHiddenTextareaValue);

  // When the page first loads, set the language syntax highlighting
  // and ensure the hidden textarea mirrors the Ace Editor
  updateEditorLanguage();
  updateHiddenTextareaValue();
  updateEditorHeight();
}

function updateEditorLanguage() {
  editor.getSession().setMode("ace/mode/" + languages[document.getElementById("language_dropdown").value]);
}

function updateHiddenTextareaValue() {
  document.getElementById("hidden_textarea").value = editor.getValue();
}

function updateEditorHeight() {
  document.getElementById("editor").style.height = String((editor.getSession().getScreenLength() + 2) * editor.renderer.lineHeight) + "px";
  editor.resize();
}

document.addEventListener("DOMContentLoaded", onPageLoad);
</script>
