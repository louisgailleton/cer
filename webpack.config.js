var Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build/')
    // .setPublicPath('/public/build/')
    // only needed for CDN's or sub-directory deploy
    .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('eleveCalendarStyle', './assets/styles/calendar.css')
    .addEntry('moniteurCalendarStyle', './assets/styles/moniteur.css')
    .addEntry('crudAdminStyle', './assets/styles/crudAdmin.css')
    .addEntry('crudIndispoStyle', './assets/styles/crudIndispo.css')
    .addEntry('evalPre', './assets/styles/evalPre.css')
    .addEntry('contrat', './assets/styles/contrat.css')
    .addEntry('boutique', './assets/styles/boutique.css')
    .addEntry('accueilChoix', './assets/styles/accueilChoix.css')
    .addEntry('code', './assets/styles/code.css')
    .addEntry('choixForfaitCss', './assets/styles/choixForfait.css')
    .addEntry('formInscription', './assets/styles/formInscription.css')
    .addEntry('login', './assets/styles/login.css')
    .addEntry('mesInformations', './assets/styles/mesInformations.css')
    .addEntry('secretaireCalendarCss','./assets/styles/secretaireCalendar.css')
    .addEntry('listeEleves','./assets/styles/elevesListe.css')
    .addEntry('examenCss','./assets/styles/examenCss.css')
    .addEntry('commandes','./assets/styles/commandes.css')
    .addEntry('porteOuverte','./assets/styles/admin/porteOuverte.css')
    .addEntry('creaCompte','./assets/styles/creaCompte.css')
    .addEntry('forfait','./assets/styles/forfait.css')

    //.addEntry('page1', './assets/page1.js')
    //.addEntry('page2', './assets/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/admin.js')
    .addEntry('formulaire', './assets/eleveFormulaire.js')
    .addEntry('informations', './assets/eleveInformations.js')
    .addEntry('choixForfait', './assets/eleveChoixForfait.js')
    .addEntry('monDossier', './assets/monDossier.js')
    .addEntry('porteOuverteJs', './assets/porteOuverte.js')
    .addEntry('evalPreP1', './assets/evalPre/evalPreP1.js')
    .addEntry('evalPreP2', './assets/evalPre/evalPreP2.js')
    .addEntry('evalPreP3', './assets/evalPre/evalPreP3.js')

    .addEntry('crud', './assets/crud.js')
    .addEntry('crudLieu', './assets/crudLieu.js')
    .addEntry('eleveCalendar', './assets/eleveCalendar.js')
    .addEntry('moniteurCalendar', './assets/moniteurCalendar.js')
    .addEntry('secretaireCalendar', './assets/secretaire/secretaireCalendar.js')
    .addEntry('modalMdp', './assets/admin/modalMdp.js')
    .addEntry('boutiqueJS', './assets/boutique.js')
    .addEntry('listeElevesJs', './assets/secretaire/listeEleves.js')
    .addEntry('accueilChoixJs', './assets/accueilChoix.js')
    .addEntry('paginator', './assets/secretaire/paginator.js')
;

module.exports = Encore.getWebpackConfig();
