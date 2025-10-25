import React, { useState } from "react";

function ImageWithFallback({ src, fallback, alt, ...props }) {
  const [imgSrc, setImgSrc] = useState(src);

  return (
    <img
      {...props}
      src={imgSrc}
      alt={alt}
      onError={() => setImgSrc(fallback)}
    />
  );
}

export default ImageWithFallback;
