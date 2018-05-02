'use strict';

// required //
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    compass = require('gulp-compass'),
    imagemin = require('gulp-imagemin'),
    sourcemaps = require('gulp-sourcemaps'),
    vinylbuffer = require('vinyl-buffer'),
    source = require('vinyl-source-stream'),
    modernizr = require('gulp-modernizr'),
    browserify = require('browserify'),
    uglify = require('gulp-uglify'),
    autoprefixer = require('gulp-autoprefixer'),
    cleanCSS = require('gulp-clean-css'),
    eslint = require('gulp-eslint'),
    rename = require('gulp-rename'),
    changed = require('gulp-changed'),
    pngquant = require('pngquant');

//destination of files
var webroot = "../public/",
    devroot = "./";


var dis = {
    js: webroot + "js/",
    css: webroot + "css/",
    font: webroot + "fonts/",
    image: webroot + "img/"
};

var dev = {
    js: devroot + "scripts/**/*.js",
    js_pth: devroot + "scripts",
    sassY: devroot + "sass/**/*.scss",
    img: devroot + "img/**/*"
};



gulp.task('sass-compile', function () {
    //var format = argv.production ? 'compressed' : 'expanded';

    return gulp.src(dev.sassY)
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true }))

        .on('error', function (err) {
            console.error(err.message);
        })
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('.'))

        .pipe(gulp.dest(dis.css))

});

/* Image compression
 * -------------------- */
gulp.task('image-compression', function () {
    return gulp.src(dev.img)
        .pipe(imagemin({
            progressive: true,
            optimizationLevel: 5,
            interlaced: true,
            svgoPlugins: [{ removeViewBox: false }],
            use: [pngquant(192, '--quality', '60-80', '--nofs', '-')]
        }))
        .pipe(gulp.dest(dis.image));
});

gulp.task('fonts', function () {
    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest(dis.font))
})

var config = {
    // config browserfy
    browserify: {
        // Enable source maps
        debug: true,
        // Additional file extensions to make optional
        extensions: ['.coffee', '.hbs'],
        // A separate bundle will be generated for each
        // bundle config in the list below
        bundleConfigs: [{
            entries: dev.js_pth + '/main.js',
            dest: dis.js,
            outputName: 'main.min.js'
        }]
    }
};

/*************************************************************************************/
/* Browserify et all
 * -------------------- */

gulp.task('scripts', function (callback) {
    var bIfy = config.browserify;
    var bundleQueue = bIfy.bundleConfigs.length;

    var browserifyThis = function (bundleConfig) {
        // first lets see if we can build out a custom modernizr
        gulp.src(dev.js_pth + '/*.js')
            .pipe(modernizr())
            .pipe(gulp.dest(dev.js_pth + "/generated"));

        var bundler = browserify({
            // Required watchify args
            cache: {}, packageCache: {}, fullPaths: false,
            // Specify the entry point of your app
            entries: bundleConfig.entries,
            // Add file extentions to make optional in your requires
            extensions: bIfy.extensions,
            // Enable source maps!
            debug: config.debug
        });

        var bundle = function () {
            // Log when bundling starts
            //bundleLogger.start(bundleConfig.outputName);

            return bundler
                .bundle()
                // Report compile errors
                //.on('error', handleErrors)
                // Use vinyl-source-stream to make the
                // stream gulp compatible. Specifiy the
                // desired output filename here.
                .pipe(source(bundleConfig.outputName))
                // Specify the output destination
                .pipe(vinylbuffer())
                .pipe(uglify())
                .pipe(gulp.dest(bundleConfig.dest));
            //.on('end', reportFinished);
        };

        var reportFinished = function () {
            // Log when bundling completes
            //bundleLogger.end(bundleConfig.outputName)

            if (bundleQueue) {
                bundleQueue--;
                if (bundleQueue === 0) {
                    // If queue is empty, tell gulp the task is complete.
                    // https://github.com/gulpjs/gulp/blob/master/docs/API.md#accept-a-callback
                    callback();
                }
            }
        };

        return bundle();
    };

    // Start bundling with Browserify for each bundleConfig specified
    bIfy.bundleConfigs.forEach(browserifyThis);
});


gulp.task('build', ['fonts', 'image-compression', 'sass-compile']);
