'use strict';

module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    require('time-grunt')(grunt);

    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // Define the configuration for all the tasks
    grunt.initConfig({
        uglify: {
            my_target: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'Assets/build/contactserver.min.js.map'
                },
                files: {
                    'Assets/build/contactserver.min.js': ['Assets/js/libraries/*.js', 'Assets/js/*.js']
                }
            }
        },
        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1,
                sourceMap: true,
                root: 'Assets/build',
                sourceMapName: 'Assets/build/contactserver.min.css.map'
            },
            target: {
                files: {
                    'Assets/build/contactserver.min.css': ['Assets/css/libraries/*.css', 'Assets/css/*.css']
                }
            }
        },
        watch: {
            js: {
                files: ['Assets/js/libraries/*.js', 'Assets/js/*.js'],
                tasks: ['uglify']
            },
            css: {
                files: ['Assets/css/libraries/*.css', 'Assets/css/*.css'],
                tasks: ['cssmin']
            }
        }
    });

    grunt.registerTask('default', ['uglify', 'cssmin']);
};
