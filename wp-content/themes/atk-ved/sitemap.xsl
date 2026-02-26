<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>XML Sitemap - АТК ВЭД</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <style type="text/css">
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
                        font-size: 14px;
                        color: #333;
                        background: #f5f5f5;
                        margin: 0;
                        padding: 20px;
                    }
                    .container {
                        max-width: 1200px;
                        margin: 0 auto;
                        background: #fff;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        overflow: hidden;
                    }
                    .header {
                        background: linear-gradient(135deg, #e31e24 0%, #c01a1f 100%);
                        color: #fff;
                        padding: 30px;
                        text-align: center;
                    }
                    .header h1 {
                        margin: 0 0 10px 0;
                        font-size: 28px;
                        font-weight: 600;
                    }
                    .header p {
                        margin: 0;
                        opacity: 0.9;
                    }
                    .info {
                        background: #f8f9fa;
                        padding: 20px 30px;
                        border-bottom: 1px solid #e9ecef;
                    }
                    .info p {
                        margin: 0 0 10px 0;
                        color: #666;
                    }
                    .info a {
                        color: #e31e24;
                        text-decoration: none;
                    }
                    .info a:hover {
                        text-decoration: underline;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    thead {
                        background: #f8f9fa;
                    }
                    th {
                        text-align: left;
                        padding: 15px 20px;
                        font-weight: 600;
                        color: #333;
                        border-bottom: 2px solid #e9ecef;
                    }
                    td {
                        padding: 12px 20px;
                        border-bottom: 1px solid #e9ecef;
                    }
                    tr:hover {
                        background: #f8f9fa;
                    }
                    .url {
                        word-break: break-all;
                        color: #e31e24;
                    }
                    .priority-high {
                        color: #28a745;
                        font-weight: 600;
                    }
                    .priority-medium {
                        color: #ffc107;
                        font-weight: 600;
                    }
                    .priority-low {
                        color: #6c757d;
                    }
                    .footer {
                        padding: 20px 30px;
                        text-align: center;
                        color: #666;
                        font-size: 12px;
                        border-top: 1px solid #e9ecef;
                    }
                    .count {
                        background: #e31e24;
                        color: #fff;
                        padding: 3px 10px;
                        border-radius: 12px;
                        font-size: 12px;
                        margin-left: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>XML Sitemap</h1>
                        <p>АТК ВЭД - Товары из Китая для маркетплейсов</p>
                    </div>
                    
                    <div class="info">
                        <p><strong>Что это?</strong> Это карта сайта в формате XML для поисковых систем Google, Яндекс, Bing.</p>
                        <p><strong>Количество URL:</strong> <span class="count"><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></span></p>
                        <p><strong>Последнее обновление:</strong> <xsl:value-of select="sitemap:urlset/sitemap:url[1]/sitemap:lastmod"/></p>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="50%">URL</th>
                                <th width="15%">Приоритет</th>
                                <th width="20%">Частота</th>
                                <th width="15%">Изменён</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="sitemap:urlset/sitemap:url">
                                <tr>
                                    <td>
                                        <a class="url" href="{sitemap:loc}" target="_blank">
                                            <xsl:value-of select="sitemap:loc"/>
                                        </a>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="sitemap:priority &gt;= 0.8">
                                                <span class="priority-high"><xsl:value-of select="sitemap:priority"/></span>
                                            </xsl:when>
                                            <xsl:when test="sitemap:priority &gt;= 0.5">
                                                <span class="priority-medium"><xsl:value-of select="sitemap:priority"/></span>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <span class="priority-low"><xsl:value-of select="sitemap:priority"/></span>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                    <td>
                                        <xsl:value-of select="sitemap:changefreq"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="sitemap:lastmod"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        <p>Generated by ATK VED Theme v1.9.0 | <a href="https://atk-ved.ru.local/">Вернуться на сайт</a></p>
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
