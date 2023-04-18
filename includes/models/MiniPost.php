<?php

class MiniPost {
    protected ?int $id;
    protected Image $image;

    private function __construct(?int $id, Image $image) {
        $this->id = $id;
        $this->image = $image;
    }

    function __destruct() { }

    /**
     * Get the post's ID.
     * @returns int The ID.
     */
    function getId() {
        return $this->id;
    }

    /**
     * Get the image associated with the post.
     * @returns Image The image.
     */
    function getPostImage() {
        return $this->image;
    }

    /**
     * Create a new post using database information.
     * @param int $id The ID of the post;
     * @param int $imageId The ID of the image;
     * @param string $imageBlob The Blob of the image associated with the post.
     * @param string $imageAlt The Alt text for the image associated with the post.
     * @return MiniPost A Permanent post.
     */
    static function parseMiniPost(int $id, int $imageId, string $imageBlob, string $imageAlt) {

        $image = new Image(
            $imageId,
            $imageBlob,
            $imageAlt
        );

        return new MiniPost(
            $id,
            $image
        );
    }
}