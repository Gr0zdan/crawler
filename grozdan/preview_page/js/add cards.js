
function addNewDiv(/*need to add arguments to customise every card*/){
    var div = document.createElement("DIV");
    div.className = "card";
    div.id = prompt("title : ");
        var h3 = document.createElement("H3");
        var h3Text = document.createTextNode(div.id);
        h3.appendChild(h3Text);
        h3.className = "title";
        div.appendChild(h3);
            var img = document.createElement("IMG");
            img.className = "card-img";
            img.src = prompt("add image src : ");
            img.alt = "image";
            div.appendChild(img);
                var p = document.createElement("P");
                p.className = "info";
                var pText = document.createTextNode(prompt("add info : "));
                p.appendChild(pText);
                div.appendChild(p);
                    var rating = document.createElement("P");
                    rating.className = "rating";
                    var ratingValue = document.createTextNode(prompt("rating : "));
                    rating.appendChild(ratingValue);
                    div.appendChild(rating);
                        var a = document.createElement("A");
                        a.href = prompt("add magnet link : ");
                        a.className = "download-button";
                        var aText = document.createTextNode("download");
                        a.appendChild(aText);
                        div.appendChild(a);
    document.getElementsByClassName("grid-container")[0].appendChild(div);

}

function removeLastDiv() {
    var divs = document.getElementsByClassName("card");
    divs[divs.length - 1].remove();
}
