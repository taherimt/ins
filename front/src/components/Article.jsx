import { Nav, Card, Col, Row } from "react-bootstrap";
import '../ArticlesGrid.css';

const Article = ({ articles }) => {
	return (
		<Col md={10}>
			<Row xs={1} md={3} className="g-4">
				{articles.map((article) => (
					<Col key={article.id}>
						<Card className="h-100 shadow-sm">
							<Card.Img
								variant="top"
								src={article.image || "https://placehold.co/1280x750"}
								style={{ objectFit: 'cover', height: '200px' }}
							/>
							<Card.Body>
								<Card.Title>{article.title}</Card.Title>
								<Card.Subtitle className="mb-2 text-muted">
									<div>Author: <span className="text-primary">{article.author}</span></div>
									<div>Source: <span className="text-primary">{article.source.name}</span></div>
									<div>Published: {new Date(article.published_at).toLocaleDateString()}</div>
									<div>Category: <span className="text-info">{article.category.name}</span></div>
								</Card.Subtitle>
								<Card.Text>
									{article.description}...
								</Card.Text>
								<Card.Link href={article.url} target="_blank" className="stretched-link">
									Read full article
								</Card.Link>
							</Card.Body>
						</Card>
					</Col>
				))}
			</Row>
		</Col>
	);
};

export default Article;
