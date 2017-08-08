function updateSnippetLikes(snippet_id, response) {
    var like_dislike_counts = response.split("|");
    document.getElementById("like_snippet_count_" + snippet_id).innerHTML = like_dislike_counts[0];
    document.getElementById("dislike_snippet_count_" + snippet_id).innerHTML = like_dislike_counts[1];
}

function addLikeButtonCallback(like_dislike, snippet_id) {
    document.getElementById(like_dislike + "_snippet_button_" + snippet_id).addEventListener("click", function(ev) {
        req = new XMLHttpRequest();
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
                updateSnippetLikes(snippet_id, this.responseText);
        };
        req.open("GET", "processes/" + like_dislike + "_snippet.php?id=" + snippet_id, true);
        req.send();

        ev.preventDefault();
        return false;
    });
}