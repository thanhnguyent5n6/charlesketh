"use strict";

var AssessmentResult = require("../values/AssessmentResult.js");
var isUndefined = require("lodash/isUndefined");
var MissingArgument = require("../errors/missingArgument");
/**
 * @param {object} app The app
 * @param {object} args An arguments object with usedKeywords, searchUrl, postUrl,
 * @param {object} args.usedKeywords An object with keywords and ids where they are used.
 * @param {string} args.searchUrl The url used to link to a search page when multiple usages of the keyword are found.
 * @param {string} args.postUrl The url used to link to a post when 1 usage of the keyword is found.
 * @constructor
 */
var PreviouslyUsedKeyword = function PreviouslyUsedKeyword(app, args) {
    if (isUndefined(app)) {
        throw new MissingArgument("The previously keyword plugin requires the YoastSEO app");
    }
    if (isUndefined(args)) {
        args = {
            usedKeywords: {},
            searchUrl: "",
            postUrl: ""
        };
    }
    this.app = app;
    this.usedKeywords = args.usedKeywords;
    this.searchUrl = args.searchUrl;
    this.postUrl = args.postUrl;
};
/**
 * Registers the assessment with the assessor.
 *
 * @returns {void}
 */
PreviouslyUsedKeyword.prototype.registerPlugin = function () {
    this.app.registerAssessment("usedKeywords", {
        getResult: this.assess.bind(this),
        isApplicable: function isApplicable(paper) {
            return paper.hasKeyword();
        }
    }, "previouslyUsedKeywords");
};
/**
 * Updates the usedKeywords.
 *
 * @param {object} usedKeywords An object with keywords and ids where they are used.
 * @returns {void}
 */
PreviouslyUsedKeyword.prototype.updateKeywordUsage = function (usedKeywords) {
    this.usedKeywords = usedKeywords;
};
/**
 * Scores the previously used keyword assessment based on the count.
 *
 * @param {object} previouslyUsedKeywords The result of the previously used keywords research
 * @param {Paper} paper The paper object to research.
 * @param {Jed} i18n The i18n object.
 * @returns {object} the scoreobject with text and score.
 */
PreviouslyUsedKeyword.prototype.scoreAssessment = function (previouslyUsedKeywords, paper, i18n) {
    var count = previouslyUsedKeywords.count;
    var id = previouslyUsedKeywords.id;
    if (count === 0) {
        return {
            text: i18n.dgettext("js-text-analysis", "You've never used this focus keyword before, very good."),
            score: 9
        };
    }
    if (count === 1) {
        var url = "<a href='" + this.postUrl.replace("{id}", id) + "' target='_blank'>";
        return {
            /* Translators: %1$s and %2$s expand to an admin link where the focus keyword is already used. %3$s and %4$s
            expand to a link to an article on yoast.com about why you should not use a keyword more than once. */
            text: i18n.sprintf(i18n.dgettext("js-text-analysis", "You've used this focus keyword %1$sonce before%2$s. " + "It???s probably a good idea to read about %3$swhy you should not use your focus keyword more than once%4$s."), url, "</a>", "<a href='https://yoa.st/20x' target='_blank' rel='noopener noreferrer'>", "</a>"),
            score: 6
        };
    }
    if (count > 1) {
        url = "<a href='" + this.searchUrl.replace("{keyword}", encodeURIComponent(paper.getKeyword())) + "' target='_blank'>";
        return {
            /* Translators: %1$s and $3$s expand to the admin search page for the focus keyword, %2$d expands to the number
            of times this focus keyword has been used before, %4$s and %5$s expand to a link to an article on yoast.com
            about why you should not use a keyword more than once. */
            text: i18n.sprintf(i18n.dgettext("js-text-analysis", "You've used this focus keyword %1$s%2$d times before%3$s. " + "It???s probably a good idea to read about %4$swhy you should not use your focus keyword more than once%5$s."), url, count, "</a>", "<a href='https://yoa.st/20x' target='_blank' rel='noopener noreferrer'>", "</a>"),
            score: 1
        };
    }
};
/**
 * Researches the previously used keywords, based on the used keywords and the keyword in the paper.
 *
 * @param {Paper} paper The paper object to research.
 * @returns {{id: number, count: number}} The object with the count and the id of the previously used keyword
 */
PreviouslyUsedKeyword.prototype.researchPreviouslyUsedKeywords = function (paper) {
    var keyword = paper.getKeyword();
    var count = 0;
    var id = 0;
    if (!isUndefined(this.usedKeywords[keyword])) {
        count = this.usedKeywords[keyword].length;
        id = this.usedKeywords[keyword][0];
    }
    return {
        id: id,
        count: count
    };
};
/**
 * The assessment for the previously used keywords.
 *
 * @param {Paper} paper The Paper object to assess.
 * @param {Researcher} researcher The Researcher object containing all available researches.
 * @param {object} i18n The locale object.
 * @returns {AssessmentResult} The assessment result of the assessment
 */
PreviouslyUsedKeyword.prototype.assess = function (paper, researcher, i18n) {
    var previouslyUsedKeywords = this.researchPreviouslyUsedKeywords(paper);
    var previouslyUsedKeywordsResult = this.scoreAssessment(previouslyUsedKeywords, paper, i18n);
    var assessmentResult = new AssessmentResult();
    assessmentResult.setScore(previouslyUsedKeywordsResult.score);
    assessmentResult.setText(previouslyUsedKeywordsResult.text);
    return assessmentResult;
};
module.exports = PreviouslyUsedKeyword;
//# sourceMappingURL=previouslyUsedKeywords.js.map
//# sourceMappingURL=previouslyUsedKeywords.js.map
