/**
* Template Name: Mentor
* Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

})();
// script.js


// public/js/collaborateuraddmain.js
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileUpload');
    const previewContainer = document.getElementById('file-preview-container');

    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function(event) {
            previewContainer.innerHTML = ''; // Vider le conteneur avant d'ajouter de nouveaux fichiers

            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const filePreview = document.createElement('div');
                filePreview.className = 'file-preview-item';

                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                filePreview.appendChild(fileName);

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Supprimer';
                removeButton.type = 'button';
                removeButton.addEventListener('click', function() {
                    removeFileFromInput(file, filePreview);
                });

                filePreview.appendChild(removeButton);
                previewContainer.appendChild(filePreview);
            }
        });

        function removeFileFromInput(fileToRemove, previewElement) {
            const dataTransfer = new DataTransfer();
            const files = fileInput.files;

            for (let i = 0; i < files.length; i++) {
                if (files[i] !== fileToRemove) {
                    dataTransfer.items.add(files[i]);
                }
            }

            fileInput.files = dataTransfer.files;
            previewElement.remove(); // Supprimer l'élément de l'aperçu
        }
    } else {
        console.error("Élément fileUpload ou file-preview-container introuvable.");
    }
});
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("competitionForm");

  form.addEventListener("submit", function (event) {
      if (!form.checkValidity()) {
          event.preventDefault(); // Empêche la soumission si HTML5 détecte une erreur
          event.stopPropagation();
      }

      form.classList.add("was-validated"); // Ajoute une classe pour afficher les erreurs
  }, false);
});

 // Validation du formulaire
 document.getElementById('competitionForm').addEventListener('submit', function(event) {
  let valid = true;

  // Réinitialiser les erreurs
  resetErrors();

  // Récupérer les champs du formulaire
  let nomEntreprise = document.getElementById('{{ form.nom_entreprise.vars.id }}');
  let nomComp = document.getElementById('{{ form.nom_comp.vars.id }}');
  let dateDebut = document.getElementById('{{ form.date_debut.vars.id }}');
  let dateFin = document.getElementById('{{ form.date_fin.vars.id }}');
  let description = document.getElementById('{{ form.description.vars.id }}');
  let fichierFile = document.getElementById('{{ form.fichierFile.vars.id }}');

  // Validation des champs
  if (!nomEntreprise.value.trim()) {
      valid = false;
      showError('nomEntrepriseError', 'Le nom de l\'entreprise est obligatoire.');
  }

  if (!nomComp.value.trim() || nomComp.value.length < 3) {
      valid = false;
      showError('nomCompError', 'Le nom de la compétition doit contenir au moins 3 caractères.');
  }

  if (!dateDebut.value.trim()) {
      valid = false;
      showError('dateDebutError', 'Veuillez sélectionner une date de début.');
  }

  if (!dateFin.value.trim()) {
      valid = false;
      showError('dateFinError', 'Veuillez sélectionner une date de fin.');
  }

  if (!description.value.trim() || description.value.length < 10) {
      valid = false;
      showError('descriptionError', 'La description doit contenir au moins 10 caractères.');
  }

  if (fichierFile.files.length === 0) {
      valid = false;
      showError('fichierFileError', 'Veuillez télécharger un fichier.');
  }

  // Si la validation échoue, empêcher l'envoi du formulaire
  if (!valid) {
      event.preventDefault();
  }
});

// Fonction pour réinitialiser les erreurs
function resetErrors() {
  document.querySelectorAll('.invalid-feedback').forEach(function(errorElement) {
      errorElement.textContent = '';
  });
}

// Fonction pour afficher les erreurs
function showError(errorId, message) {
  document.getElementById(errorId).textContent = message;
}


function toggleMenu(element) {
  // Ferme tous les autres menus ouverts
  const openMenus = document.querySelectorAll('.dropdown-content');
  openMenus.forEach(menu => {
      if (menu !== element.querySelector('.dropdown-content')) {
          menu.style.display = 'none';
      }
  });

  // Affiche ou cache le menu actuel
  const dropdown = element.querySelector('.dropdown-content');
  if (dropdown.style.display === 'block') {
      dropdown.style.display = 'none';
  } else {
      dropdown.style.display = 'block';
  }
}document.getElementById('searchCompetition').addEventListener('keyup', function() {
  let query = this.value.trim();
  console.log("Recherche en cours :", query); // Debugging

  if (query === "") {
      document.querySelector('.competition-grid').innerHTML = '<p>Aucune compétition trouvée.</p>';
      return;
  }

  fetch(`/competition/search/${encodeURIComponent(query)}`)
      .then(response => response.json())
      .then(data => {
          console.log("Résultats retournés :", data); // Debugging

          let container = document.querySelector('.competition-grid');
          container.innerHTML = ''; // Clear existing competitions

          if (data.length === 0) {
              container.innerHTML = '<p>Aucune compétition trouvée.</p>';
          } else {
              data.forEach(comp => {
                  let competitionDiv = `
                      <div class="competition">
                          <h2>${comp.nomComp}</h2>
                          <p class="date">Du ${comp.dateDebut} au ${comp.dateFin}</p>
                          <p class="description">${comp.description}</p>
                          <p>Organisée par : ${comp.nomEntreprise}</p>
                      </div>
                  `;
                  container.innerHTML += competitionDiv;
              });
          }
      })
      .catch(error => console.error('Erreur AJAX:', error));
});
