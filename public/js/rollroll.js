//On récupère la hauteur du rollroll
var rollH = $("#lottery").height();
console.log(rollH);

// Durée du roulement en secondes
var duree = 10;

// Nombre de répétition de la liste
var nbList = 10;

function makeList() {
	// On copie la liste template et on la colle dans la liste definitive autant de fois que définit dans nbList
	for (var i=1; i<=nbList; i++) {
		$("#lottery #template li").clone().appendTo("#lottery #playersList");
	}
	// On décale la liste d'un cran vers le bas pour que le roll soit vide au début
	$("#lottery #playersList").css('top', rollH);
}

function rollroll() {
	// On calcule la position cible de l'animation
	var position = (nbList*(rollH*10))-rollH;
	//On lance l'animation : on décale le conteneur vers le haut jusqu'a la position calculée dans le temps définit dans duree
	$("#lottery #playersList").animate({top:-position},duree*1000,function(){
		   $(".screenCenter").fireworks();
	});
}

// On lance la création de la liste
makeList();

//On lance le rollroll automatiquement
//rollroll();

// On lance l'animation quand on appuie sur une touche
$('body').keyup(function(e){
   if(e.keyCode == 32){
	   // Barre espace
	   rollroll();
   }
});