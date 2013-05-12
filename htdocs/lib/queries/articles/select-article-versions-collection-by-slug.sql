SELECT
    av.articleVersionId,
    av.dateCreated
FROM
    article a
    JOIN articleVersion av
        ON a.articleId = av.parentArticleId
        AND a.slug = ?;