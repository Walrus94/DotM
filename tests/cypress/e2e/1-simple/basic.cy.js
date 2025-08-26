describe('page loads as admin', () => {
    let date = new Date();

    [
        "/",
        "/artist.php",
        "/better.php",
        "/blog.php",
        "/bookmarks.php",
        "/comments.php",
        "/contest.php",
        "/donate.php",
        "/forums.php",
        "/inbox.php",
        "/index.php",
        "/locked.php",
        "/logchecker.php",
        "/login.php?action=recover",
        "/rules.php",
        "/staff.php",
        "/staffpm.php",
        "/stats.php",
        "/tools.php",
        "/tools.php?action=analysis_list",
        "/torrents.php",
        "/torrents.php?action=advanced&artistname=doesnotexist",
        "/user.php",
        "/user.php?id=1",
        "/user.php?action=edit&id=1",
        "/user.php?action=invite",
        "/user.php?action=notify",
        "/user.php?action=search&search=aaa",
        "/userhistory.php?action=subscriptions",
        "/userhistory.php?action=posts",
        "/view.php",
        "/wiki.php",
    ].forEach((url) => {
        beforeEach(() => {
            cy.loginAdmin();
        })
        it(`should have a footer: ${url}`, () => {
            cy.visit(url);
            cy.ensureFooter();
        })
    })
})
