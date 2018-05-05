<head>
    <meta charset="utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />

    {# seo #}
    <title>{% if seo['title'] %}{{ seo['title']|trim|escape }}{% if !preg_match('@rankval@ui', seo['title']) %} :: {{ global_var['domain'] }}{% endif %}{% else %}{{ global_var['domain'] }}{% endif %}</title>
    {% if seo['description'] %}
        <meta name="description" content="{{ seo['description']|trim|escape_attr }}" />
    {% endif %}
    {% if seo['keywords'] %}
        <meta name="keywords" content="{{ seo['keywords']|trim|escape_attr }}" />
    {% endif %}
    {% if seo['title'] or seo['og_title'] %}
        <meta name="title" content="{% if seo['og_title'] %}{{ seo['og_title']|trim|escape_attr }}{% else %}{{ seo['title']|trim|escape_attr }}{% endif %}" />
    {% endif %}

    {# og #}
    {% if seo['og_title'] or seo['title'] %}
        <meta property="og:title" content="{% if seo['og_title'] %}{{ seo['og_title']|trim|escape_attr }}{% else %}{{ seo['title']|trim|escape_attr }}{% endif %}" />
    {% endif %}

    {% if seo['og_url'] %}
        <meta property="og:url" content="http://{{ global_var['domain'] }}{{ seo['og_url'] }}" />
        <link rel="canonical" href="http://{{ global_var['domain'] }}{{ seo['og_url'] }}" />
    {% endif %}

    {% if seo['og_description'] or seo['description'] %}
        <meta property="og:description" content="{% if seo['og_description'] %}{{ seo['og_description']|trim|escape_attr }}{% else %}{{ seo['description']|trim|escape_attr }}{% endif %}" />
    {% endif %}

    {% if seo['og_image'] %}
        {% if preg_match('@^(ht|f)tp(s)?:\/\/@ui', seo['og_image']) %}
            <meta property="og:image" content="{{ seo['og_image'] }}" />
        {% elseif preg_match('@^\/\/@ui', seo['og_image']) %}
            <meta property="og:image" content="http:{{ seo['og_image'] }}" />
        {% else %}
            <meta property="og:image" content="http://{{ global_var['domain'] }}{{ seo['og_image'] }}" />
        {% endif %}
    {% endif %}

    {% if seo['noindex'] or !global_var['production'] %}
    <meta name="robots" content="noindex" />
    <meta name="googlebot" content="noindex" />
    {% endif %}

    <meta property="og:site_name" content="{{ global_var['domain'] | upper }}" />


    <!-- Vendor styles -->
    <link rel="stylesheet" href="/front/fonts/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="/front/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="/front/fonts/pe-icon-7-stroke/css/helper.css" />

    <link rel="stylesheet" href="/front/bootstrap-metismenu/metisMenu.css" />
    <link rel="stylesheet" href="/front/animate.css/animate.css" />
    <link rel="stylesheet" href="/front/bootstrap/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="/front/frontend-v1/styles/theme.css">
    <link rel="stylesheet"  href="/assets/frontend.css?{{ global_var['version'] }}"  type="text/css" />

    <link rel="icon"        href="/front/favicon.ico?{{ global_var['version'] }}"         type="image/x-icon" />
</head>
