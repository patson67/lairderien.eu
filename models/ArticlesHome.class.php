<?php
/**
* @file : ArticlesHome.class.php // PascalCase
*
*/
class ArticlesHome {
    // Déclaration des propriétés privées.
    private $id;
    private $titre;
    private $content;
    private $url_image;
    private $visibility;

    private $link;

    //...

    public function __construct($link)
    {
        $this->link = $link;
    }

    // Getter/Setter | Accesseur/Mutateur | Accessor/Mutator


    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getContent() {
        return $this->content;
    }

    public function getUrl_image() {
        return $this->url_image;
    }

    public function getVisibility() {
        return $this->visibility;
    }

    // 
    
    public function setTitre( $titre ) {
        if ( strlen( $titre ) < 3 ) 
            throw new Exception ("Titre trop court (< 3)");
        else if ( strlen( $titre ) > 127 )
            throw new Exception ("Titre trop long (> 127)");            
        $this->titre = $titre;
    }

    public function setContent( $content ) {
        if ( strlen( $content ) < 3 ) 
            throw new Exception ("Content trop court0 (< 3)");
        else if ( strlen( $content ) > 2047 )
            throw new Exception ("Content trop long (> 2047)");            
        $this->content = $content;
    }

    public function setUrl_image( $url_image ) {
        if ( strlen( $url_image ) < 3 ) 
            throw new Exception ("Mot de passe trop court (< 3)");
        else if ( strlen( $url_image ) > 255 )
            throw new Exception ("Mot de passe trop long (> 255)");            
        $this->url_image = $url_image;
    }
    
    public function setVisibility( $visibility ) {
        if($visibility != 0 || $visibility != 1)
            throw new exception('mauvaise valeur pour visibility');     
        $this->visibility = $visibility;
    }
}
?>