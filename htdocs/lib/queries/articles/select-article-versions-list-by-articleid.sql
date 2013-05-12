SELECT
    av.articleVersionId,
    av.dateCreated
FROM
    article a
    JOIN articleVersion av
        ON a.articleId = av.parentArticleId
WHERE 1 = 1
    AND a.articleId = ?;