import { useState, useEffect } from "react";
import axios from "axios";
import {useAuth} from "../contexts/AuthContext";

// This hook is for getting all articles
const useArticleList = (url) => {
	const [loading, setLoading] = useState(false);
	const [error, setError] = useState(false);
	const [articles, setArticles] = useState([]);
	const [page, setPage] = useState(1);
	const [lastPage, setLastPage] = useState(0);
	const [total, setTotal] = useState(0);
	const { loginStorageData } = useAuth();
	useEffect(() => {
		(async () => {
			setLoading(true);
			try {
				const response = await axios.get(url, {
					headers: {
						Authorization: `Bearer ${loginStorageData.token}` // Add the Authorization header
					}
				});
				if (response.data) {
					setArticles([...response.data.articles]);
					setLastPage(response.data.lastPage);
					setPage(response.data.page);
					setTotal(response.data.total);
				}
			} catch (error) {
				console.error(error);
				setError(true);
			}
			setLoading(false);
		})();
	}, [url]);

	return { loading, error, articles, total, page, lastPage };
};

export default useArticleList;
