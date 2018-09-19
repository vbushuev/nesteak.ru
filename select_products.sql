select p.id,b.name,p.post_title,m.meta_value ,s.meta_value,w.meta_value
from ns_posts p
	join ns_postmeta m on m.post_id = p.id and m.meta_key='_sku'
	join ns_postmeta s on s.post_id = p.id and s.meta_key = '_regular_price'
	join ns_postmeta w on w.post_id = p.id and w.meta_key like '_weight'
	join ns_term_relationships br on br.object_id = p.id  join ns_term_taxonomy bt on bt.term_taxonomy_id = br.term_taxonomy_id and bt.taxonomy = 'pwb-brand' join ns_terms b on b.term_id = bt.term_id
where p.post_type='product'
order by p.post_title
