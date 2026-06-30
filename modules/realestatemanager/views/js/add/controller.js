/**
 * Real Estate – Contrôleur d'orchestration du formulaire d'ajout.
 *
 * Rôle :
 *  - charge l'idProperty initial depuis le DOM
 *  - initialise chaque module d'étape
 *  - branche le stepper sur les boutons précédent/suivant/publier
 *  - délègue la sauvegarde à chaque module via son interface (collect/validate)
 *
 * N'a AUCUNE connaissance des champs spécifiques d'une étape.
 * Pour ajouter une étape :
 *   1. créer un partial steps/stepN-xxx.tpl
 *   2. créer un module steps/stepN-xxx.js implémentant { init, collect, validate }
 *   3. l'enregistrer dans STEP_MODULES ci-dessous
 *   4. bumper TOTAL_STEPS
 */
(function (global) {
  'use strict';

  var TOTAL_STEPS = 5;
  var STEP_MODULES_KEYS = ['step1', 'step2', 'step3', 'step4', 'step5'];
  var UI = global.RealEstateUI;
  var API = global.RealEstateAPI;

  document.addEventListener('DOMContentLoaded', function () {
    var idPropertyInput = document.getElementById('re-id-property');
    var idProperty = parseInt((idPropertyInput && idPropertyInput.value) || 0, 10);

    // Contexte partagé entre l'orchestrateur et les modules
    var ctx = {
      getIdProperty: function () {
        return idProperty;
      },
      setIdProperty: function (id) {
        idProperty = parseInt(id, 10) || 0;
        if (idPropertyInput) idPropertyInput.value = idProperty;
      },
    };

    // Initialisation des modules d'étape
    var modules = {};
    STEP_MODULES_KEYS.forEach(function (key) {
      var mod = global.RealEstateSteps && global.RealEstateSteps[key];
      if (!mod) {
        console.warn('[RealEstate] module manquant : ' + key);
        return;
      }
      mod.init(ctx);
      modules[mod.step] = mod;
    });

    // Stepper
    var stepper = new global.RealEstateStepper({
      totalSteps: TOTAL_STEPS,
      allowJumpForward: idProperty > 0, // en édition, toutes les étapes sont accessibles
      onChange: function () {
        UI.refreshIcons(); // ré-instancie les icônes Lucide après changement de panneau
      },
    });

    // En édition, on considère toutes les étapes comme déjà visitées
    if (idProperty > 0) stepper.markAllVisited();
    stepper.init();

    // Navigation
    var nextBtn = document.getElementById('re-next-btn');
    var prevBtn = document.getElementById('re-prev-btn');
    var finishBtn = document.getElementById('re-finish-btn');

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        saveCurrentStep().then(function () {
          stepper.next();
        });
      });
    }
    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        stepper.prev();
      });
    }
    if (finishBtn) {
      finishBtn.addEventListener('click', function () {
        saveCurrentStep().then(function () {
          UI.showSaveIndicator('Annonce publiée !', true);
          setTimeout(function () {
            window.location.href = global.RE_MYPROP_URL;
          }, 800);
        });
      });
    }

    /**
     * Sauvegarde l'étape courante. Retourne une promesse résolue uniquement si OK.
     */
    function saveCurrentStep() {
      var current = stepper.currentStep;
      var module = modules[current];
      if (!module) return Promise.reject(new Error('Module introuvable'));

      var validation = module.validate();
      if (!validation.ok) {
        // alert(validation.message);
        UI.notifications.error(validation.message);
        return Promise.reject(new Error(validation.message));
      }

      UI.showSaveIndicator('Sauvegarde…');
      return API.saveStep(current, module.collect(), ctx.getIdProperty())
        .then(function (data) {
          if (data.id_property) {
            ctx.setIdProperty(data.id_property);
          }
          UI.showSaveIndicator('Sauvegardé', true);
        })
        .catch(function (err) {
          // alert(err.message || 'Erreur lors de l\'enregistrement');
          UI.notifications.error(err.message || "Erreur lors de l'enregistrement");
          UI.showSaveIndicator('Erreur');
          throw err;
        });
    }
  });
})(window);
