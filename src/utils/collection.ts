import type { CollectionEntry } from 'astro:content';

export function getBlogParams(post: CollectionEntry<'blog'>) {
  const [path, year, month, slug] = post.slug.match(/(\d{4})\/(\d{2})\/(.+)/);

  return {
    year,
    month,
    path,
    slug,
  };
}