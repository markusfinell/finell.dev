import type { CollectionEntry } from 'astro:content';

export function getBlogParams(post: CollectionEntry<'blog'>) {
  //console.log(post);
  const [id, year, month, day, slug] = post.id.match(/(\d{4})-(\d{2})-(\d{2})-([^.]+)/);

  return {
    title: post.data.title,
    year,
    month,
    day,
    slug
  };
}