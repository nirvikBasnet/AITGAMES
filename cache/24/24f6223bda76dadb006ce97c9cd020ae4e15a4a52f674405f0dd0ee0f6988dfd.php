<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* home.twig */
class __TwigTemplate_7e471b7970d346ec0ae802c6c1d9754a88094bcb30933b1e88ea33b92e5f2024 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!doctype html>
<html lang=\"en\">
    <head>
        <title></title>
        <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">
        <link rel=\"stylesheet\" href=\"node_modules/bootstrap/dist/css/bootstrap.min.css\">
        <script src=\"node_modules/jquery/dist/jquery.min.js\"></script>
        <script src=\"node_modules/popper.js/dist/umd/popper.min.js\"></script>
        <script src=\"node_modules/bootstrap/dist/js/bootstrap.min.js\"></script>
    </head>
    <body>
        <main class=\"container\">
            <div class=\"row\">
                ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["products"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 15
            echo "                <div class=\"col-md-3\" data-id=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["product"], "product_id", []), "html", null, true);
            echo "\">
                    <h3>";
            // line 16
            echo twig_escape_filter($this->env, $this->getAttribute($context["product"], "name", []), "html", null, true);
            echo "</h3>
                    <h6>";
            // line 17
            echo twig_escape_filter($this->env, $this->getAttribute($context["product"], "price", []), "html", null, true);
            echo "</h6>
                    <p>";
            // line 18
            echo twig_escape_filter($this->env, $this->getAttribute($context["product"], "description", []), "html", null, true);
            echo "</p>
                </div>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 21
        echo "            </div>
        </main>
    </body>
</html>";
    }

    public function getTemplateName()
    {
        return "home.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 21,  62 => 18,  58 => 17,  54 => 16,  49 => 15,  45 => 14,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "home.twig", "/mnt/c/Users/PC/projects/phpClassD19/templates/home.twig");
    }
}
