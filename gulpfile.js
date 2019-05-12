/* Gulp General Packages */
"use strict";
const gulp = require("gulp");
const path = require("path");
const themeDirectory = `web/app/themes/whatjackhasmade`;

/* SCSS/CSS Packages */
const sass = require("gulp-sass");
const sassGlob = require("gulp-sass-glob");
const plumber = require("gulp-plumber");
const notify = require("gulp-notify");
const cleanCSS = require("gulp-clean-css");
const autoprefixer = require("gulp-autoprefixer");

/* Browsersync Packages */
const browserSync = require("browser-sync").create();

/* JS Babel and Minifcation Packages */
const babel = require("gulp-babel");
const concat = require("gulp-concat");
const sourcemaps = require("gulp-sourcemaps");
const uglify = require("gulp-uglify-es").default;

/* Gulp Task: SCSS Compiling */
gulp.task("sass", function() {
	return gulp
		.src(`${themeDirectory}/assets/styles/styles.scss`)
		.pipe(customPlumber("Error running Sass"))
		.pipe(sassGlob())
		.pipe(sass())
		.pipe(autoprefixer({ browsers: ["last 2 versions"], cascade: false }))
		.pipe(
			cleanCSS({ debug: true }, details => {
				console.log(`${details.name}: ${details.stats.originalSize}`);
				console.log(`${details.name}: ${details.stats.minifiedSize}`);
			})
		)
		.pipe(gulp.dest(`${themeDirectory}`))
		.pipe(browserSync.stream());
});

/* Gulp Task: JavaScript Compiling */
gulp.task("scripts", function() {
	return gulp
		.src([
			`${themeDirectory}/assets/scripts/jquery.js`,
			`${themeDirectory}/assets/scripts/svgxuse.js`,
			`${themeDirectory}/assets/scripts/script.js`
		])
		.pipe(sourcemaps.init())
		.pipe(concat(`${themeDirectory}/site.js`))
		.pipe(babel())
		.pipe(uglify())
		.pipe(sourcemaps.write(`./`))
		.pipe(gulp.dest(`./`))
		.pipe(browserSync.stream());
});

/* Gulp Task: Browsersync change on file updates */
gulp.task("watch", ["sass", "scripts"], function() {
	browserSync.init({
		proxy: "local-whatjackhasmade.co.uk"
	});

	gulp.watch([`${themeDirectory}/assets/scripts/**/*.js`], ["scripts"]);
	gulp.watch([`${themeDirectory}/assets/styles/**/*.scss`], ["sass"]);
	gulp.watch(`${themeDirectory}/*.php`).on("change", browserSync.reload);
});

/* Gulp Task: Custom error message handling in console */
function customPlumber(errTitle) {
	return plumber({
		errorHandler: notify.onError({
			title: errTitle || "Error running Gulp",
			message: "Error: <%= error.message %>"
		})
	});
}

/* Gulp Task: run `gulp` in the terminal */
gulp.task("default", ["sass", "scripts", "watch"]);
