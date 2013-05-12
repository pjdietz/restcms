SELECT
    av.articleVersionId,
    av.dateCreated
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
WHERE 1 = 1
    AND a.slug = ?;