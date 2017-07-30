<form class="card snippet-search">
  <i class="fa fa-search" aria-hidden="true"></i>
  <input id="snippet_search" type="text" placeholder="<?=(empty($_GET['q']) ? 'Search snippets' : $_GET['q'])?>" name="q" />
  <input type="submit" />
</form>
<script type="text/javascript">
// Focus the searchbox when the page loads
document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("snippet_search").focus();
});
</script>
