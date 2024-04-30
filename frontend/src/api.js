const apiUrl = import.meta.env.DEV
  ? "http://api.finell.test/wp-json"
  : "https://api.finell.dev/wp-json";

export default {
  url: apiUrl,
  getPostsByCategory: async (slug) => {
    return await fetch(`${apiUrl}/wp/v2/categories/?slug=${slug}`)
      .then((res) => res.json())
      .then(async (categories) => {
        const res = await fetch(
          `${apiUrl}/wp/v2/posts/?categories=${categories.pop().id}`
        );
        return await res.json();
      });
  },
  stripTags: (str) => str.replace(/(<([^>]+)>)/gi, ""),
  blockClass: (attrs) => {
    const replacements = {
      style: "",
      textAlign: "text-%s",
      align: "text-%s",
      backgroundColor: "bg-%s",
      textColor: "clr-%s",
      sizeSlug: "size-%s",
      fontSize: "fs-%s",
      hasParallax: (hasParallax) => hasParallax && "has-parallax",
      isDark: (isDark) => isDark && "has-dark-bg",
      layout: (layout) => {
        const isRow = layout?.type === "flex" && !layout?.orientation;
        if (isRow && layout?.flexWrap === "wrap") return "cluster";
        if (isRow) return "row";
        if (layout?.type === "flex" && layout?.orientation === "vertical")
          return "stack";
        return;
      },
    };

    const classes = [];
    for (const key in attrs) {
      if (!replacements.hasOwnProperty(key)) continue;

      if (typeof replacements[key] === "function") {
        classes.push(replacements[key](attrs[key]));
      } else {
        classes.push(replacements[key].replace("%s", attrs[key]));
      }
    }

    return classes;
  },
};
