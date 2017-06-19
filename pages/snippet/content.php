<form id="edit_post_form" action="/processes/edit_snippet.php" method="post">
  <input class="post_title top" type="text" name="title" placeholder="Title" value="<?=$post_title?>"><!--
--><select class="privacy-dropdown" name="privacy">
    <option value="public">Public</option>
    <option value="private">Private</option>
  </select><!--
--><select class="language-dropdown" name="language">
    <option value="c++">C++</option>
    <option value="c#">C#</option>
    <option value="c">C</option>
    <option value="java">Java</option>
    <option value="javascript">Javascript</option>
    <option value="php">PHP</option>
    <option value="lua">LUA</option>
  </select><!--
--><textarea name="content" placeholder="// Code here..."></textarea>
</form>
<div class="button-container">
  <input form="edit_post_form" class="button right" type="submit" name="submit" value="<?=$save_button_text?>">
</div>
