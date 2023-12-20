import { Dropdown, InputGroup, Row, FormControl, DropdownButton, Container, Button, ButtonGroup } from "react-bootstrap";
import { ArrowLeftSquareFill, ArrowRightSquareFill } from "react-bootstrap-icons";
import Sidebar from "./Sidebar";
import { useEffect, useState } from "react";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import Slider from "react-slick";

import useArticleList from "../hooks/useArticleList";
import Article from "./Article";
import { useAuth } from "../contexts/AuthContext";
import Loading from "./Loading";
import '../Home.css';
function Home() {
	const { loginStorageData } = useAuth();
	const [searchValue, setSearchValue] = useState("");
	const [orderBy, setOrderBy] = useState("desc");
	const [selectedDate, setSelectedDate] = useState("");
	const [selectedSources, setSelectedSources] = useState([]);
	const [selectedCategory, setSelectedCategory] = useState([]);
	const [pageNo, setPageNo] = useState(1);
	const [articlesShow, setArticlesShow] = useState([]);
	const userId = loginStorageData.user.id;

	const api = `http://127.0.0.1:8000/api/articles?s=${searchValue}&sort=${orderBy}&date=${selectedDate}&category=${selectedCategory}&source=${selectedSources}&page=${pageNo}&user=${userId}`;

	const { articles, lastPage, loading } = useArticleList(api);

	useEffect(() => {
		setArticlesShow([...articles]);
	}, [articles]);

	const handleSources = (value) => {
		setSelectedSources(value);
	};

	const handleDate = (value) => {
		setSelectedDate(value);
	};

	const handleCategory = (value) => {
		setSelectedCategory(value);
	};
	const sliderSettings = {
		dots: true,
		infinite: true,
		speed: 500,
		slidesToShow: 1,
		slidesToScroll: 1
	};

	function truncateText(text, maxLength) {
		if (text.length <= maxLength) return text;
		return text.substring(0, maxLength) + '...';
	}

	const handlePagination = (event) => {
		const status = event.target.id;
		if (status === "next") {
			setPageNo(pageNo + 1);
		} else if (status === "prev") {
			setPageNo(pageNo - 1);
		} else if (status === "last") {
			// setPageNo(lastPage);
		} else if (status === "first") {
			setPageNo(1);
		} else {
			setPageNo(pageNo);
		}
	};

	return (
		<Container className="mt-2 min-height">

			{/* Slider for the first three news articles */}
			<div className="main-slider">
				<Slider {...sliderSettings}>
					{articles.slice(0, 3).map((article, index) => (
						<div  key={index} className="slider-item">
							<div className="slider-title">
								<h3>{truncateText(article.title, 50)}</h3>
							</div>
							<div >
								<img src={article.image || "https://placehold.co/1280x750"} alt={article.title} style={{ width: '100%', height: 'auto' }} />
							</div>
						</div>
					))}
				</Slider>
			</div>




			{loading && !searchValue && <Loading />}
			<Row>
				<InputGroup className="my-3 search-bar">
					<FormControl
						type="search"
						placeholder="Search articles..."
						value={searchValue}
						onChange={(e) => setSearchValue(e.target.value)}
						className="bg-light border-0 search-input"
					/>
					<DropdownButton
						as={InputGroup.Append}
						variant="outline-secondary"
						title="Sort Order"
						onSelect={(eventKey) => setOrderBy(eventKey)}
						className="sort-dropdown"
					>
						<Dropdown.Item eventKey="asc">Ascending</Dropdown.Item>
						<Dropdown.Item eventKey="desc">Descending</Dropdown.Item>
					</DropdownButton>
				</InputGroup>


				<Article articles={articlesShow} />
				<Sidebar selectedSources={handleSources} selectedDate={handleDate} selectedCategory={handleCategory} />
			</Row>



			{articles.length > 0 && (
				<nav className="d-flex justify-content-center mt-4 mb-5 pagination-nav">
					<ButtonGroup>
						<Button disabled={pageNo <= 1} id="prev" onClick={handlePagination} variant="outline-primary" className="pagination-btn">
							<ArrowLeftSquareFill /> Prev
						</Button>
						{pageNo > 1 && (
							<Button id="first" onClick={handlePagination} variant="outline-primary" className="pagination-btn">
								1
							</Button>
						)}
						{/* Consider adding page numbers in between for larger page counts */}
						<Button disabled={pageNo >= lastPage} id="next" onClick={handlePagination} variant="outline-primary" className="pagination-btn">
							Next <ArrowRightSquareFill />
						</Button>
					</ButtonGroup>
				</nav>
			)}
		</Container>

	);
}

export default Home;
