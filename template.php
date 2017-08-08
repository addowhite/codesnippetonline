<?php

/**
 * Reads a file, executes any php content and returns the resulting file
 * @param string $file_path A path to the file starting from (not including) the base url
 * @return string The processed contents of the file
 */
function get_file_contents($file_path) {
  // Start the output buffer
  ob_start();

  // Read a file into the buffer (while executing any php content)
  include $file_path;

  // Read the current contents of the buffer into a variable
  $buffer = ob_get_contents();

  // Close the output buffer
  ob_end_clean();

  // Return the processed content of the file
  return $buffer;
}

/**
 * Read a template file and insert given information into the template
 * @param string $template_path The path to the template file starting from (not including) the base url
 * @param array $params An associative array of template parameters and their values
 * @return string The contents of the template with all the template parameters filled in
 */
function template($template_path, $params = array()) {
  // Create all the parameters as local variables in their own right
  if ($params != NULL) extract($params);

  // Start the output buffer
  ob_start();

  // Read the file into the buffer (while executing any php)
  include $template_path;

  // Read the current contents of the buffer into a variable
  $buffer = ob_get_contents();

  // Close the output buffer
  ob_end_clean();

  // Return the completed template
  return $buffer;
}

/**
 * Build a standard page using the page template
 * @param string $content_path The path to the file that contains the content for the page
 * @param array $params The template parameters to use for the content file when inserting the content into the page template
 * @return string The processed 'page' template with a processed 'content' template embedded in it
 */
function page($content_path, $params = NULL) {
  if ($params == NULL) $params = array();
  return template(
    "templates/basic_page.php",
    $params + array("content" => template($content_path, $params))
  );
}

/**
 * Build a standard page containing a card which displays a message
 * @param string $error_message The message to display on the page
 * @return string The processed 'error_page' template with a card containing the text from $error_message
 */
function error_page($error_message) {
  return page('templates/error_page.php', array('error_message' => "{$error_message}"));
}

/**
 * Build a standard button using the button template
 * @param string $id The id to use for the html element
 * @param string $value The string to use for the button text
 * @param string $href The target url of the button (the destination for when it is clicked)
 * @return string A string containing the markup for a single button
 */
function button($id, $value, $href, $align = 'right') {
  return template("templates/button.php", array(
    "id"    => $id,
    "value" => $value,
    "href"  => $href,
    "align" => $align
  ));
}

/**
 * Build a standard snippet using the snippet template
 * @param int The database id of the snippet
 * @param string The title of the snippet
 * @param string The content of the snippet (the code)
 * @param string The CodeSnippetOnline name of the programming language the snippet is written in
 * @param string The chosen display name of the user that wrote the snippet
 * @return string A string containing the markup for a single snippet
 */
function snippet($snippet_id, $title, $content, $language, $author, $like_count, $dislike_count) {
  return template("templates/snippet.php", array(
    "snippet_id"    => $snippet_id,
    "title"         => $title,
    "content"       => $content,
    "language"      => $language,
    "author"        => $author,
    "like_count"    => $like_count,
    "dislike_count" => $dislike_count
  ));
}

?>
