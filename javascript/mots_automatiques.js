$( document ).ready(function() {

  var stop = false;
  var nombre_articles = $(".articles li").length;
  var nombre_essais = 0;

  $('.numerateur').html('0');
  $('.denominateur').html(nombre_articles);
  $('.avancement').attr('max', nombre_articles).attr('value', 0);

  $("#commencer").click(function(){
    stop = false;
    nombre_essais = 0;
    extraire($(".articles li:not(.fait)").first());
    $('.alerte_fenetre').show();
    $(this).hide();
    $("#arreter").show();
  });

  $("#arreter").click(function(){
    arreter();
  });

  function arreter(){
    stop = true;
    nombre_essais = 0;
    $("#commencer").show();
    $("#arreter").hide();
    $('.alerte_fenetre').hide();
  }

  function extraire(article) {
    var next = article.next();
    if(!stop) {
      article.show();
      var id_article = article.attr('data-id');
      $.ajax({
        type: "POST",
        url : "?exec=_mots_automatiques",
        data: { id_article : id_article },
        success: function(x){
          console.log(x);
          if(x != 'erreur' || nombre_essais >= 3) {
            article.hide().addClass('fait');
            $('.numerateur').html(article.index() + 1);
            $('.avancement').attr('value', article.index() + 1);
            extraire(next);
            nombre_essais = 0;
          } else if(nombre_essais < 3) {
            nombre_essais++;
            extraire(article);
          }
        },
        error : function(){
        	 console.log("Problème");
           arreter();
        }
   	  });
    }
  }


});
