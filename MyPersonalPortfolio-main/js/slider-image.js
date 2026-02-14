/*const bilder = [
    "bild1.jpg",
    "bild2.jpg",
    "bild3.jpg"
];

let aktuellerIndex = 0;
const bildElement = document.getElementById("slider-img");

function naechstesBild() {
    aktuellerIndex++;
    // Wenn am Ende angekomme starte von vorne
    if(aktuellerIndex >=bilder.length){
        aktuellerIndex = 0;
    }
    bildElement.src = bilder[aktuellerIndex];
}

function vorherige(){
    aktuellerIndex--;
// Wenn am Anfang angekommen  springe dann  letzten Bild
    if(aktuellerIndex < 0);{
    aktuellerIndex = bilder.length -1;
    }
    bildElement.src = bilder[aktuellerIndex];
}
*/

// Make your slider image clickable
const sliderImg = document.getElementById("slider-img");
const modal = document.getElementById("meinModal");
const modalBild = document.getElementById("modal-bild");

// Function for oppen
sliderImg.onclick = function() {
    modal.style.display = "block";
    modalBild.src = this.src; // Takes the current image from the slider
}

// Function for close
function schließeModal() {
    modal.style.display = "none";
}

// click close, if you someware next to the Picture
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}