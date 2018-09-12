
{component_define_params params=[ 'tag', 'image', 'header', 'content' ]}

<{$tag|default:"div"} class="media">
  <a class="pull-left" href="#">
      <img src="{$image}" class="media-object" data-src="{$image}">
  </a>
  <div class="media-body">
    <h4 class="media-heading">{$header}</h4>
 
    <!-- Nested media object -->
    <div class="media">
      {$content}
    </div>
  </div>
</{$tag|default:"div"}>