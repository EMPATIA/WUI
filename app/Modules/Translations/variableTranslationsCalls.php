<?php

/*
 * This file was created to have the variable Strings IDs imported by the Translations Manager.
 * It's used in cases when a part of the String ID is variable but can only take known values, like:
 * Contents:
 *      We know the Content Types that exist, but doesn't makes sense to have chained Ifs or Switches
 *      for every type, so we can call it like this: Cms::cms.$contentTypeName, where $contentTypeName has
 *      some limited possible values: Pages, Events, Highlights and News.
 *
 * This way, the code is cleaner but we can still have a perfectly-working Translation Manager
 *
 * The Strings should be called like always, but we just don't use the returned values, ie:
 *      If our String Id is "form.back" we should use:
 *          trans("form.back");
 *  and continue our life like nothing happened, but in the next Translations Import, that string will be included.
 *
 * Always :
 *  > group translations by Module/keys just to keep this file as clean as possible
 *  > keep the calls inside a comment block, just in case the file is executed, doesn't run everything at all
 */

/*
    trans("oneCommentDefault.positiveComments");
    trans("oneCommentDefault.neutralComments");
    trans("oneCommentDefault.negativeComments");

    trans("oneComment.positiveComments");
    trans("oneComment.neutralComments");
    trans("oneComment.negativeComments");

    trans("privateSidebar.Departamento");

    trans("publicUserTimeline.votesTitle");
    trans("publicUserTimeline.topicsTitle");
    trans("publicUserTimeline.postsTitle");

    trans("publicUserTimeline.positiveVote");
    trans("publicUserTimeline.positivelyVote");
    trans("publicUserTimeline.negativeVote");
    trans("publicUserTimeline.negativelyVote");

    trans("monthNames.month-1");
    trans("monthNames.month-2");
    trans("monthNames.month-3");
    trans("monthNames.month-4");
    trans("monthNames.month-5");
    trans("monthNames.month-6");
    trans("monthNames.month-7");
    trans("monthNames.month-8");
    trans("monthNames.month-9");
    trans("monthNames.month-01");
    trans("monthNames.month-02");
    trans("monthNames.month-03");
    trans("monthNames.month-04");
    trans("monthNames.month-05");
    trans("monthNames.month-06");
    trans("monthNames.month-07");
    trans("monthNames.month-08");
    trans("monthNames.month-09");
    trans("monthNames.month-10");
    trans("monthNames.month-11");
    trans("monthNames.month-12");

    trans("monthNames.month_min-1");
    trans("monthNames.month_min-2");
    trans("monthNames.month_min-3");
    trans("monthNames.month_min-4");
    trans("monthNames.month_min-5");
    trans("monthNames.month_min-6");
    trans("monthNames.month_min-7");
    trans("monthNames.month_min-8");
    trans("monthNames.month_min-9");
    trans("monthNames.month_min-01");
    trans("monthNames.month_min-02");
    trans("monthNames.month_min-03");
    trans("monthNames.month_min-04");
    trans("monthNames.month_min-05");
    trans("monthNames.month_min-06");
    trans("monthNames.month_min-07");
    trans("monthNames.month_min-08");
    trans("monthNames.month_min-09");
    trans("monthNames.month_min-10");
    trans("monthNames.month_min-11");
    trans("monthNames.month_min-12");

    trans("privateSidebar.departments");
    trans("empatiaSiteEthics.use_terms");
    trans("empatiaSiteEthics.privacy_policy");

    trans("privateEntitiesDivided.manage_vat_numbers");
    trans("privateEntitiesDivided.manage_domain_names");

    trans("privateContentManager.title_pages")
    trans("privateContentManager.title_events")
    trans("privateContentManager.title_news")
    trans("privateContentManager.pages")
    trans("privateContentManager.events")
    trans("privateContentManager.news")
*/