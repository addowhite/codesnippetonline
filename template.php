<?php

function get_file_contents($file_path) {
  ob_start();
  include $file_path;
  $buffer = ob_get_contents();
  ob_end_clean();
  return $buffer;
}

function template($template_path, $params) {
  // Create all the parameters as local variables in their own right
  if ($params != NULL) extract($params);

  ob_start();
  include $template_path;
  $buffer = ob_get_contents();
  ob_end_clean();
  return $buffer;
}

function page($content_path, $params = NULL) {
  if ($params == NULL) $params = array();
  return template(
    "templates/basic_page.php",
    $params + array("content" => template($content_path, $params))
  );
}

function button($id, $value, $href) {
  return template("templates/button.php", array(
    "id"    => $id,
    "value" => $value,
    "href"  => $href
  ));
}

function snippet($snippet_id, $title, $content, $language, $author) {
  return template("templates/snippet.php", array(
    "snippet_id" => $snippet_id,
    "title"      => $title,
    "content"    => $content,
    "language"   => $language,
    "author"     => $author
  ));
}

?>
