module.exports = function (grunt) {
    require('load-grunt-tasks')(grunt);
    grunt.loadNpmTasks('grunt-contrib-sass');

    grunt.initConfig({
                         sass        : {
                             dev : {
                                 options: {
                                     style    : 'expanded',
                                     sourcemap: 'auto'
                                 },
                                 files  : [{
                                     expand: true,
                                     cwd   : 'assets/src/scss',
                                     src   : ['*.scss'],
                                     dest  : 'assets/dist/style',
                                     ext   : '.css'
                                 }]
                             },
                             dist: {
                                 options: {
                                     style    : 'compressed',
                                     sourcemap: 'none'
                                 },
                                 files  : [{
                                     expand: true,
                                     cwd   : 'assets/src/scss',
                                     src   : ['*.scss'],
                                     dest  : 'assets/dist/style',
                                     ext   : '.css'
                                 }]
                             }
                         },
                         autoprefixer: {
                             dev : {
                                 options: {
                                     browsers: ['last 5 versions'],
                                     map     : true
                                 },
                                 files  : {
                                     'assets/dist/style/main.css': 'assets/dist/style/main.css'
                                 }
                             },
                             dist: {
                                 options: {
                                     map: false
                                 },
                                 files  : {
                                     'assets/dist/style/main.css': 'assets/dist/style/main.css'
                                 }
                             }
                         },
                         uglify      : {
                             dist: {
                                 files: [{
                                     expand: true,
                                     cwd   : 'assets/dist/js',
                                     src   : '**/*.js',
                                     dest  : 'assets/dist/js'
                                 }]
                             }
                         },
                         clean       : {
                             css: ['assets/dist/style'],
                             js : ['assets/dist/js']
                         },
                         copy        : {
                             js: {
                                 expand: true,
                                 cwd   : 'assets/src/js',
                                 src   : '**',
                                 dest  : 'assets/dist/js/'
                             }
                         },
                         watch       : {
                             css_dev: {
                                 files  : ['assets/src/**/*.scss'],
                                 tasks  : ['css_dev'],
                                 options: {
                                     interrupt: true
                                 }
                             },
                             js_dev : {
                                 files  : ['assets/src/**/*.js'],
                                 task   : ['js_dev'],
                                 options: {
                                     interrupt: true
                                 }
                             }
                         }
                     });

    grunt.registerTask('css_dev', ['clean:css', 'sass:dev', 'autoprefixer:dev']);
    grunt.registerTask('css_dist', ['clean:css', 'sass:dist', 'autoprefixer:dist']);

    grunt.registerTask('js_dev', ['clean:js', 'copy:js']);
    grunt.registerTask('js_dist', ['clean:js', 'copy:js', 'uglify:dist']);
};