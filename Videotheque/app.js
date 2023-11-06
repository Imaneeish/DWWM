var films = [
    {
        title: "Deadpool",
        years: 2016,
        authors: "Tim Miller"
    },
    {
        title: "Spiderman",
        years: 2002,
        authors: "Sam Raimi"
    },
    {
        title: "Scream",
        years: 1996,
        authors: "Wes Craven"
    },
    {
        title: "It: chapter 1",
        years: 2019,
        authors: "Andy Muschietti"
    }
];
// TODO

function updateFilmTable() {
    var filmTableBody = document.getElementById("filmTableBody");
    filmTableBody.innerHTML = '';

    films.forEach(function (film, index) {
        var row = filmTableBody.insertRow();
        row.innerHTML = `
            <td>${film.title}</td>
            <td>${film.years}</td>
            <td>${film.authors}</td>
            <td><button class="btn btn-danger" onclick="deleteFilm(${index})">Supprimer</button></td>
        `;
    });
}

function deleteFilm(index) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce film ?")) {
        films.splice(index, 1);
        updateFilmTable();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    updateFilmTable();

    var addFilmForm = document.getElementById("addFilmForm");
    addFilmForm.addEventListener("submit", function (event) {
        event.preventDefault();

        var title = document.getElementById("title").value;
        var year = document.getElementById("year").value;
        var author = document.getElementById("author").value;

        // Valider les données du formulaire
        if (title.length < 2 || year < 1900 || year > new Date().getFullYear() || author.length < 5) {
            alert("Erreur dans le formulaire. Vérifiez les données saisies.");
        } else {
            // Ajouter le film à la liste
            films.push({ title: title, years: year, authors: author });
            updateFilmTable();

            // Effacer les champs du formulaire
            document.getElementById("title").value = '';
            document.getElementById("year").value = '';
            document.getElementById("author").value = '';

            // Afficher un message d'alerte
            alert("Film ajouté avec succès");
        }
    });
});

