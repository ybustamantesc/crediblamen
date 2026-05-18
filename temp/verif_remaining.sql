SELECT a.code, a.name, a.naturaleza,
ROUND(COALESCE(SUM(e.debit - e.credit),0),2) AS raw,
CASE WHEN a.naturaleza='deudora' THEN ROUND(COALESCE(SUM(e.debit - e.credit),0),2)
     WHEN a.naturaleza='acreedora' THEN ROUND(-COALESCE(SUM(e.debit - e.credit),0),2)
     ELSE ROUND(COALESCE(SUM(e.debit - e.credit),0),2) END AS display
FROM tb_account a
LEFT JOIN tb_journal_entry e ON e.account_id = a.id
LEFT JOIN tb_journal j ON j.id = e.journal_id AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)
WHERE a.code IN ('14010101301','54030901201','54050801201','54059901201','54059901301','63010201201')
GROUP BY a.id
ORDER BY a.code;
