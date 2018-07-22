$(document).ready(function(){
  $("#submission").on("click", submitLyrics);
  $("#reset").on("click", resetText);
  $("#input_lyrics").val("");
});

var submitLyrics = function(){
  var lyrics = $("#input_lyrics").val();
  $("#results").hide();
  $("#thinking").show();
  $.ajax({
    method: "POST",
    url: "scripts/lyricsGenerator.php",
    data: {lyrics: lyrics}
  }).done(function(ret) {
    $("#result_lyrics").text('"' + ret + '"');
    $("#thinking").hide();
    $("#results").show();
  });
}

var resetText = function(){
  $("#input_lyrics").val("");
  $("#result_lyrics").text("");
  $("#results").hide();
}



