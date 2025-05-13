<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* chat.html.twig */
class __TwigTemplate_b3c2012010858a434d125bb3fd07d3ce extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'body' => [$this, 'block_body'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "chat.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "chat.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "Symfony Docs Chatbot";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 6
        yield "    ";
        yield from $this->yieldParentBlock("stylesheets", $context, $blocks);
        yield "
    <link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css\">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        .chat-header {
            padding: 1rem;
            text-align: center;
            font-size: 1.75rem;
            font-weight: bold;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .chat-wrapper {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-welcome {
            text-align: center;
            padding: 2rem 1rem;
            font-size: 1.25rem;
            color: #333;
        }

        .chat-messages {
            padding: 1rem;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            background-color: #fafafa;
        }

        .chat-form {
            display: flex;
            border-top: 1px solid #ddd;
            padding: 0.5rem;
        }

        .chat-form input[type=\"text\"] {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .chat-form button {
            margin-left: 0.5rem;
            padding: 0 1rem;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .chat-form button:hover {
            background-color: #0056b3;
        }

        .chat-message p {
            margin: 0.5rem 0;
        }

        .chatbot-response {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #222;
            margin-top: 1rem;
        }

        .chatbot-response pre {
            background: #f0f0f0;
            padding: 1em;
            overflow-x: auto;
            border-radius: 5px;
            margin-top: 1em;
        }

        .chatbot-response code {
            font-family: Consolas, Monaco, \"Andale Mono\", monospace;
            background: #f0f0f0;
            padding: 0.2em 0.4em;
            border-radius: 3px;
        }
    </style>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 104
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 105
        yield "    <div class=\"chat-header\">
        Symfony Docs Chatbot
    </div>

    <div class=\"chat-wrapper\">
        <div class=\"chat-welcome\">
            Welcome to Symfony Docs Chatbot. We will try to answer your Symfony-based questions.
        </div>

        <div class=\"chat-messages\">
            ";
        // line 115
        if ((isset($context["question"]) || array_key_exists("question", $context) ? $context["question"] : (function () { throw new RuntimeError('Variable "question" does not exist.', 115, $this->source); })())) {
            // line 116
            yield "                <div class=\"chat-message\">
                    <p><strong>You asked:</strong> ";
            // line 117
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["question"]) || array_key_exists("question", $context) ? $context["question"] : (function () { throw new RuntimeError('Variable "question" does not exist.', 117, $this->source); })()), "html", null, true);
            yield "</p>
                    <p><strong>Application says:</strong></p>
                    <div class=\"chatbot-response\">
                        ";
            // line 120
            yield $this->env->getRuntime('Twig\Extra\Markdown\MarkdownRuntime')->convert((isset($context["answer"]) || array_key_exists("answer", $context) ? $context["answer"] : (function () { throw new RuntimeError('Variable "answer" does not exist.', 120, $this->source); })()));
            yield "
                    </div>
                </div>
            ";
        } else {
            // line 124
            yield "                <p>This is an answer to your Symfony documentation page.</p>
            ";
        }
        // line 126
        yield "        </div>

        <form class=\"chat-form\" method=\"POST\" action=\"";
        // line 128
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("chat_home");
        yield "\">
            <input type=\"text\" name=\"question\" placeholder=\"Type your question...\" required>
            <button type=\"submit\">Send</button>
        </form>
    </div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 135
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 136
        yield "    ";
        yield from $this->yieldParentBlock("javascripts", $context, $blocks);
        yield "
    <script src=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js\"></script>
    <script>hljs.highlightAll();</script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "chat.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  265 => 136,  255 => 135,  241 => 128,  237 => 126,  233 => 124,  226 => 120,  220 => 117,  217 => 116,  215 => 115,  203 => 105,  193 => 104,  87 => 6,  77 => 5,  60 => 3,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Symfony Docs Chatbot{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css\">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        .chat-header {
            padding: 1rem;
            text-align: center;
            font-size: 1.75rem;
            font-weight: bold;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .chat-wrapper {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-welcome {
            text-align: center;
            padding: 2rem 1rem;
            font-size: 1.25rem;
            color: #333;
        }

        .chat-messages {
            padding: 1rem;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            background-color: #fafafa;
        }

        .chat-form {
            display: flex;
            border-top: 1px solid #ddd;
            padding: 0.5rem;
        }

        .chat-form input[type=\"text\"] {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .chat-form button {
            margin-left: 0.5rem;
            padding: 0 1rem;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .chat-form button:hover {
            background-color: #0056b3;
        }

        .chat-message p {
            margin: 0.5rem 0;
        }

        .chatbot-response {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #222;
            margin-top: 1rem;
        }

        .chatbot-response pre {
            background: #f0f0f0;
            padding: 1em;
            overflow-x: auto;
            border-radius: 5px;
            margin-top: 1em;
        }

        .chatbot-response code {
            font-family: Consolas, Monaco, \"Andale Mono\", monospace;
            background: #f0f0f0;
            padding: 0.2em 0.4em;
            border-radius: 3px;
        }
    </style>
{% endblock %}

{% block body %}
    <div class=\"chat-header\">
        Symfony Docs Chatbot
    </div>

    <div class=\"chat-wrapper\">
        <div class=\"chat-welcome\">
            Welcome to Symfony Docs Chatbot. We will try to answer your Symfony-based questions.
        </div>

        <div class=\"chat-messages\">
            {% if question %}
                <div class=\"chat-message\">
                    <p><strong>You asked:</strong> {{ question }}</p>
                    <p><strong>Application says:</strong></p>
                    <div class=\"chatbot-response\">
                        {{ answer|markdown_to_html|raw }}
                    </div>
                </div>
            {% else %}
                <p>This is an answer to your Symfony documentation page.</p>
            {% endif %}
        </div>

        <form class=\"chat-form\" method=\"POST\" action=\"{{ path('chat_home') }}\">
            <input type=\"text\" name=\"question\" placeholder=\"Type your question...\" required>
            <button type=\"submit\">Send</button>
        </form>
    </div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script src=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js\"></script>
    <script>hljs.highlightAll();</script>
{% endblock %}
", "chat.html.twig", "/Users/aasawari.sahasrabuddhe/apps/SymfonyChatBotApp/templates/chat.html.twig");
    }
}
